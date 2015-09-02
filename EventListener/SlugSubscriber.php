<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Slug\SlugException;
use Darvin\Utils\EventListener\AbstractOnFlushListener;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Slug event subscriber
 */
class SlugSubscriber extends AbstractOnFlushListener implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface              $metadataFactory  Metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, PropertyAccessorInterface $propertyAccessor)
    {
        $this->metadataFactory = $metadataFactory;
        $this->propertyAccessor = $propertyAccessor;
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
        $this->em = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();

        $entity = $args->getEntity();

        $entityClass = ClassUtils::getClass($entity);

        $meta = $this->metadataFactory->getMetadata($this->em->getClassMetadata($entityClass));

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }
        foreach ($meta['slugs'] as $slugProperty => $params) {
            if (!$this->propertyAccessor->isReadable($entity, $slugProperty)) {
                throw new SlugException(sprintf('Property "%s::$%s" is not readable.', $entityClass, $slugProperty));
            }

            $slug = $this->propertyAccessor->getValue($entity, $slugProperty);

            $slugMapItem = new SlugMapItem($slug, $entityClass, $this->getEntityId($entity, $entityClass), $slugProperty);

            $this->em->persist($slugMapItem);
        }

        $this->em->flush();
    }

    /**
     * @param object $entity Entity
     */
    protected function deleteSlugMapItems($entity)
    {
        $entityClass = ClassUtils::getClass($entity);

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
        return $this->em->getRepository(SlugMapItem::CLASS_NAME);
    }
}
