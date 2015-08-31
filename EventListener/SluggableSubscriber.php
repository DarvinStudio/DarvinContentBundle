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
use Darvin\ContentBundle\Sluggable\SluggableException;
use Darvin\ContentBundle\Sluggable\SluggableInterface;
use Darvin\Utils\EventListener\AbstractOnFlushListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Gedmo\Sluggable\SluggableListener;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Sluggable event subscriber
 */
class SluggableSubscriber extends AbstractOnFlushListener implements EventSubscriber
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Gedmo\Sluggable\SluggableListener
     */
    private $sluggableListener;

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor  Property accessor
     * @param \Gedmo\Sluggable\SluggableListener                          $sluggableListener Sluggable listener
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, SluggableListener $sluggableListener)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->sluggableListener = $sluggableListener;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'onFlush',
            'postPersist',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $this
            ->onDelete(SluggableInterface::INTERFACE_NAME, array($this, 'deleteSlugMapItems'))
            ->onUpdate(SluggableInterface::INTERFACE_NAME, array($this, 'updateSlugMapItems'));
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args Event arguments
     *
     * @throws \Darvin\ContentBundle\Sluggable\SluggableException
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->em = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();

        $entity = $args->getEntity();

        if (!$entity instanceof SluggableInterface) {
            return;
        }

        $entityClass = ClassUtils::getClass($entity);

        $properties = $this->getSlugProperties($entityClass);

        if (empty($properties)) {
            return;
        }
        foreach ($properties as $property) {
            if (!$this->propertyAccessor->isReadable($entity, $property)) {
                throw new SluggableException(sprintf('Property "%s::$%s" is not readable.', $entityClass, $property));
            }

            $slug = $this->propertyAccessor->getValue($entity, $property);

            $slugMapItem = new SlugMapItem($slug, $entityClass, $this->getEntityId($entity, $entityClass), $property);

            $this->em->persist($slugMapItem);
        }

        $this->em->flush();
    }

    /**
     * @param \Darvin\ContentBundle\Sluggable\SluggableInterface $entity Sluggable entity
     */
    protected function deleteSlugMapItems(SluggableInterface $entity)
    {
        $entityClass = ClassUtils::getClass($entity);

        $slugMapItems = $this->getSlugMapItems($entityClass, $this->getEntityId($entity, $entityClass));

        foreach ($slugMapItems as $slugMapItem) {
            $this->em->remove($slugMapItem);
        }
    }

    /**
     * @param \Darvin\ContentBundle\Sluggable\SluggableInterface $entity Sluggable entity
     */
    protected function updateSlugMapItems(SluggableInterface $entity)
    {
        $entityClass = ClassUtils::getClass($entity);

        $properties = $this->getSlugProperties($entityClass);

        if (empty($properties)) {
            return;
        }

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

    /**
     * @param string $entityClass Entity class
     *
     * @return array
     */
    private function getSlugProperties($entityClass)
    {
        $configuration = $this->sluggableListener->getConfiguration($this->em, $entityClass);

        return isset($configuration['slugs']) ? array_keys($configuration['slugs']) : array();
    }
}
