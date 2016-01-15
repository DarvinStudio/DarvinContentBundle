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
use Doctrine\ORM\Events;

/**
 * Slug map event subscriber
 */
class SlugMapSubscriber extends AbstractOnFlushListener implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Darvin\ContentBundle\Slug\SlugMapItemFactory
     */
    private $slugMapItemFactory;

    /**
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface $metadataFactory    Metadata factory
     * @param \Darvin\ContentBundle\Slug\SlugMapItemFactory  $slugMapItemFactory Slug map item factory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SlugMapItemFactory $slugMapItemFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->slugMapItemFactory = $slugMapItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
            Events::postPersist,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $this
            ->onDelete(array($this, 'deleteSlugMapItems'))
            ->onUpdate(array($this, 'updateSlugMapItems'));
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

        $doctrineMeta = $this->em->getClassMetadata($entityClass);

        $meta = $this->metadataFactory->getMetadata($doctrineMeta);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }
        foreach ($this->slugMapItemFactory->createItems($entity, $meta['slugs'], $doctrineMeta) as $slugMapItem) {
            $this->em->persist($slugMapItem);
        }

        $this->em->flush();
    }

    /**
     * @param \Darvin\Utils\Event\SlugsUpdateEvent $event Event
     */
    public function postSlugsUpdate(SlugsUpdateEvent $event)
    {
        $this->init($event->getEntityManager(), $event->getEntityManager()->getUnitOfWork());

        $changeSet = $event->getChangeSet();

        $slugMapItemRepository = $this->getSlugMapItemRepository();

        $slugMapItemUpdateQb = $this->em->createQueryBuilder()
            ->update(SlugMapItem::SLUG_MAP_ITEM_CLASS, 'o')
            ->set('o.slug', 'CONCAT(:new_slug, SUBSTRING(o.slug, :old_slug_length + 1, LENGTH(o.slug)))')
            ->where('SUBSTRING(o.slug, 1, :old_slug_length) = :old_slug');

        $entitiesToUpdate = array();

        foreach ($changeSet as $oldSlug => $newSlug) {
            foreach ($slugMapItemRepository->getSimilarSlugsBuilder($oldSlug)->getQuery()->getArrayResult() as $slugMapItem) {
                if (!isset($entitiesToUpdate[$slugMapItem['objectClass']])) {
                    $entitiesToUpdate[$slugMapItem['objectClass']] = array();
                }

                $entitiesToUpdate[$slugMapItem['objectClass']][$slugMapItem['property']] = array($oldSlug, $newSlug);
            }

            $slugMapItemUpdateQb
                ->setParameter('new_slug', $newSlug)
                ->setParameter('old_slug_length', strlen($oldSlug))
                ->setParameter('old_slug', $oldSlug)
                ->getQuery()
                ->execute();
        }
        foreach ($entitiesToUpdate as $entityClass => $properties) {
            foreach ($properties as $property => $slugs) {
                $this->em->createQueryBuilder()
                    ->update($entityClass, 'o')
                    ->set(
                        'o.'.$property,
                        sprintf('CONCAT(:new_slug, SUBSTRING(o.%s, :old_slug_length + 1, LENGTH(o.%1$s)))', $property)
                    )
                    ->where(sprintf('SUBSTRING(o.%s, 1, :old_slug_length) = :old_slug', $property))
                    ->setParameter('new_slug', $slugs[1])
                    ->setParameter('old_slug_length', strlen($slugs[0]))
                    ->setParameter('old_slug', $slugs[0])
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

        $meta = $this->metadataFactory->getMetadata($this->em->getClassMetadata($entityClass));

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

        $meta = $this->metadataFactory->getMetadata($this->em->getClassMetadata($entityClass));

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
    private function getSlugMapItems($entityClass, $entityId, array $properties = array())
    {
        return $this->getSlugMapItemRepository()->getByEntityBuilder($entityClass, $entityId, $properties)->getQuery()->getResult();
    }

    /**
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository()
    {
        return $this->em->getRepository(SlugMapItem::SLUG_MAP_ITEM_CLASS);
    }
}
