<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Refresh translatable updated at event subscriber
 */
class RefreshTranslatableUpdatedAtSubscriber implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $extendedMetadataFactory;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface                  $extendedMetadataFactory Extended metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface     $propertyAccessor        Property accessor
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager     Translatable manager
     */
    public function __construct(
        MetadataFactoryInterface $extendedMetadataFactory,
        PropertyAccessorInterface $propertyAccessor,
        TranslatableManagerInterface $translatableManager
    ) {
        $this->extendedMetadataFactory = $extendedMetadataFactory;
        $this->propertyAccessor = $propertyAccessor;
        $this->translatableManager = $translatableManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $entityClass = ClassUtils::getClass($entity);

            if (!$this->translatableManager->isTranslation($entityClass)) {
                continue;
            }

            /** @var \Knp\DoctrineBehaviors\Model\Translatable\Translation $entity */
            $translatable = $entity->getTranslatable();

            if (empty($translatable)) {
                continue;
            }

            $meta = $this->extendedMetadataFactory->getExtendedMetadata($translatable);

            if (empty($meta['updatedAt'])) {
                continue;
            }

            $this->propertyAccessor->setValue($translatable, $meta['updatedAt'], new \DateTime());

            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(ClassUtils::getClass($translatable)), $translatable);
        }
    }
}
