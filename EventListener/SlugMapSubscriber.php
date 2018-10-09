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
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Slug map event subscriber
 */
class SlugMapSubscriber implements EventSubscriber
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
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->deleteSlugMapItems($em, $entity);
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->updateSlugMapItems($em, $entity);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args Event arguments
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $em     = $args->getEntityManager();
        $entity = $args->getEntity();

        $entityClass = get_class($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }
        foreach ($this->slugMapItemFactory->createItems($entity, $meta['slugs'], $em->getClassMetadata($entityClass)) as $slugMapItem) {
            $em->persist($slugMapItem);
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
    public function slugsUpdated(SlugsUpdateEvent $event)
    {
        $changeSet = $event->getChangeSet();
        $em        = $event->getEntityManager();

        foreach ($changeSet as $oldSlug => $newSlug) {
            if (empty($oldSlug)) {
                unset($changeSet[$oldSlug]);
            }
        }
        if (empty($changeSet)) {
            return;
        }

        $slugMapItemRepository = $this->getSlugMapItemRepository($em);

        $slugMapItemUpdateQb = $em->createQueryBuilder()
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

                $em->createQueryBuilder()
                    ->update(SlugMapItem::class, 'o')
                    ->set('o.slug', 'CONCAT(:new_slug, SUBSTRING(o.slug, :old_slug_length + 1, LENGTH(o.slug)))')
                    ->where('SUBSTRING(o.slug, 1, :old_slug_length) = :old_slug')
                    ->setParameter('new_slug', $slugs[1].$separator)
                    ->setParameter('old_slug_length', strlen($slugs[0]) + strlen($separator))
                    ->setParameter('old_slug', $slugs[0].$separator)
                    ->getQuery()
                    ->execute();
                $em->createQueryBuilder()
                    ->update($entityClass, 'o')
                    ->set('o.'.$property, ':new_slug')
                    ->where(sprintf('o.%s = :old_slug', $property))
                    ->setParameter('new_slug', $slugs[1])
                    ->setParameter('old_slug', $slugs[0])
                    ->getQuery()
                    ->execute();
                $em->createQueryBuilder()
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
     * @param \Doctrine\ORM\EntityManager $em     Entity manager
     * @param object                      $entity Entity
     */
    private function deleteSlugMapItems(EntityManager $em, $entity)
    {
        $entityClass = get_class($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }

        $slugMapItems = $this->getSlugMapItems($em, $entityClass, $this->getEntityId($em, $entity, $entityClass));

        foreach ($slugMapItems as $slugMapItem) {
            $em->remove($slugMapItem);
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em     Entity manager
     * @param object                      $entity Entity
     */
    private function updateSlugMapItems(EntityManager $em, $entity)
    {
        $entityClass = get_class($entity);

        $meta = $this->extendedMetadataFactory->getExtendedMetadata($entityClass);

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }

        $properties = array_keys($meta['slugs']);

        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        foreach ($properties as $key => $property) {
            if (!isset($changeSet[$property])) {
                unset($properties[$key]);
            }
        }
        if (empty($properties)) {
            return;
        }

        $slugMapItemMeta = $em->getClassMetadata(SlugMapItem::class);
        $slugMapItems    = $this->getSlugMapItems($em, $entityClass, $this->getEntityId($em, $entity, $entityClass), $properties);

        foreach ($slugMapItems as $slugMapItem) {
            $slugMapItem->setSlug($changeSet[$slugMapItem->getProperty()][1]);

            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($slugMapItemMeta, $slugMapItem);
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em          Entity manager
     * @param object                      $entity      Entity
     * @param string                      $entityClass Entity class
     *
     * @return mixed
     */
    private function getEntityId(EntityManager $em, $entity, $entityClass)
    {
        $ids = $em->getClassMetadata($entityClass)->getIdentifierValues($entity);

        return reset($ids);
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em          Entity manager
     * @param string                      $entityClass Entity class
     * @param mixed                       $entityId    Entity ID
     * @param array                       $properties  Slug properties
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     */
    private function getSlugMapItems(EntityManager $em, $entityClass, $entityId, array $properties = [])
    {
        return $this->getSlugMapItemRepository($em)->getByEntityBuilder($entityClass, $entityId, $properties)->getQuery()->getResult();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em Entity manager
     *
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(EntityManager $em)
    {
        return $em->getRepository(SlugMapItem::class);
    }
}
