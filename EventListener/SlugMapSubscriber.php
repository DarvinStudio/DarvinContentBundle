<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Slug\SlugMapItemFactory;
use Darvin\Utils\Event\SlugsUpdateEvent;
use Darvin\Utils\EventListener\AbstractOnFlushListener;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Slug map event subscriber
 */
class SlugMapSubscriber extends AbstractOnFlushListener implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $extendedMetadataFactory;

    /**
     * @var \Darvin\ContentBundle\Slug\SlugMapItemFactory
     */
    private $slugMapItemFactory;

    /**
     * @var bool
     */
    private $flushNeeded;

    /**
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface $extendedMetadataFactory Extended metadata factory
     * @param \Darvin\ContentBundle\Slug\SlugMapItemFactory  $slugMapItemFactory      Slug map item factory
     */
    public function __construct(MetadataFactoryInterface $extendedMetadataFactory, SlugMapItemFactory $slugMapItemFactory)
    {
        $this->extendedMetadataFactory = $extendedMetadataFactory;
        $this->slugMapItemFactory = $slugMapItemFactory;

        $this->flushNeeded = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::postFlush,
            Events::postPersist,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $this
            ->onDelete([$this, 'deleteSlugMapItems'])
            ->onUpdate([$this, 'updateSlugMapItems']);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args Event arguments
     *
     * @throws \Darvin\ContentBundle\Slug\SlugException
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->init($args->getEntityManager(), $args->getEntityManager()->getUnitOfWork());

        $entity = $args->getEntity();

        $entityClass = ClassUtils::getClass($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }
        foreach ($this->slugMapItemFactory->createItems($entity, $meta['slugs'], $this->em->getClassMetadata($entityClass)) as $slugMapItem) {
            $this->em->persist($slugMapItem);
        }

        $this->flushNeeded = true;
    }

    /**
     * @param \Doctrine\ORM\Event\PostFlushEventArgs $args Event arguments
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->flushNeeded) {
            $this->flushNeeded = false;

            $args->getEntityManager()->flush();
        }
    }

    /**
     * @param \Darvin\Utils\Event\SlugsUpdateEvent $event Event
     */
    public function postSlugsUpdate(SlugsUpdateEvent $event)
    {
        $this->init($event->getEntityManager(), $event->getEntityManager()->getUnitOfWork());

        $changeSet = $event->getChangeSet();

        foreach ($changeSet as $oldSlug => $newSlug) {
            if (empty($oldSlug)) {
                unset($changeSet[$oldSlug]);
            }
        }
        if (empty($changeSet)) {
            return;
        }

        $slugMapItemRepository = $this->getSlugMapItemRepository();

        $slugMapItemUpdateQb = $this->em->createQueryBuilder()
            ->update(SlugMapItem::class, 'o')
            ->set('o.slug', ':new_slug')
            ->where('o.slug = :old_slug');

        $entitiesToUpdate = [];

        foreach ($changeSet as $oldSlug => $newSlug) {
            foreach ($slugMapItemRepository->getSimilarSlugsBuilder($oldSlug)->getQuery()->getArrayResult() as $slugMapItem) {
                if (!isset($entitiesToUpdate[$slugMapItem['objectClass']])) {
                    $entitiesToUpdate[$slugMapItem['objectClass']] = [];
                }

                $entitiesToUpdate[$slugMapItem['objectClass']][$slugMapItem['property']] = [$oldSlug, $newSlug];
            }

            $slugMapItemUpdateQb
                ->setParameter('new_slug', $newSlug)
                ->setParameter('old_slug', $oldSlug)
                ->getQuery()
                ->execute();
        }
        foreach ($entitiesToUpdate as $entityClass => $properties) {
            $config = $this->extendedMetadataFactory->getExtendedMetadata($entityClass)['slugs'];

            foreach ($properties as $property => $slugs) {
                $separator = $config[$property]['separator'];

                $this->em->createQueryBuilder()
                    ->update(SlugMapItem::class, 'o')
                    ->set('o.slug', 'CONCAT(:new_slug, SUBSTRING(o.slug, :old_slug_length + 1, LENGTH(o.slug)))')
                    ->where('SUBSTRING(o.slug, 1, :old_slug_length) = :old_slug')
                    ->setParameter('new_slug', $slugs[1].$separator)
                    ->setParameter('old_slug_length', strlen($slugs[0]) + strlen($separator))
                    ->setParameter('old_slug', $slugs[0].$separator)
                    ->getQuery()
                    ->execute();
                $this->em->createQueryBuilder()
                    ->update($entityClass, 'o')
                    ->set('o.'.$property, ':new_slug')
                    ->where(sprintf('o.%s = :old_slug', $property))
                    ->setParameter('new_slug', $slugs[1])
                    ->setParameter('old_slug', $slugs[0])
                    ->getQuery()
                    ->execute();
                $this->em->createQueryBuilder()
                    ->update($entityClass, 'o')
                    ->set(
                        'o.'.$property,
                        sprintf('CONCAT(:new_slug, SUBSTRING(o.%s, :old_slug_length + 1, LENGTH(o.%1$s)))', $property)
                    )
                    ->where(sprintf('SUBSTRING(o.%s, 1, :old_slug_length) = :old_slug', $property))
                    ->setParameter('new_slug', $slugs[1].$separator)
                    ->setParameter('old_slug_length', strlen($slugs[0]) + strlen($separator))
                    ->setParameter('old_slug', $slugs[0].$separator)
                    ->getQuery()
                    ->execute();
            }
        }
    }

    /**
     * @param object $entity Entity
     */
    protected function deleteSlugMapItems($entity)
    {
        $entityClass = ClassUtils::getClass($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }

        $slugMapItems = $this->getSlugMapItems($entityClass, $this->getEntityId($entity, $entityClass));

        foreach ($slugMapItems as $slugMapItem) {
            $this->em->remove($slugMapItem);
        }
    }

    /**
     * @param object $entity Entity
     *
     * @throws \Darvin\ContentBundle\Slug\SlugException
     */
    protected function updateSlugMapItems($entity)
    {
        $entityClass = ClassUtils::getClass($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }

        $properties = array_keys($meta['slugs']);

        $changeSet = $this->uow->getEntityChangeSet($entity);

        foreach ($properties as $key => $property) {
            if (!isset($changeSet[$property])) {
                unset($properties[$key]);
            }
        }
        if (empty($properties)) {
            return;
        }

        $slugMapItems = $this->getSlugMapItems($entityClass, $this->getEntityId($entity, $entityClass), $properties);

        foreach ($slugMapItems as $slugMapItem) {
            $slugMapItem->setSlug($changeSet[$slugMapItem->getProperty()][1]);

            $this->recomputeChangeSet($slugMapItem);
        }
    }

    /**
     * @param object $entity      Entity
     * @param string $entityClass Entity class
     *
     * @return mixed
     */
    private function getEntityId($entity, $entityClass)
    {
        $ids = $this->em->getClassMetadata($entityClass)->getIdentifierValues($entity);

        return reset($ids);
    }

    /**
     * @param string $entityClass Entity class
     * @param mixed  $entityId    Entity ID
     * @param array  $properties  Slug properties
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     */
    private function getSlugMapItems($entityClass, $entityId, array $properties = [])
    {
        return $this->getSlugMapItemRepository()->getByEntityBuilder($entityClass, $entityId, $properties)->getQuery()->getResult();
    }

    /**
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository()
    {
        return $this->em->getRepository(SlugMapItem::class);
    }
}
