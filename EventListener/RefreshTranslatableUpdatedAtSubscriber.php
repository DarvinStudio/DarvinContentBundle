<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
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
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface              $extendedMetadataFactory Extended metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor        Property accessor
     */
    public function __construct(MetadataFactoryInterface $extendedMetadataFactory, PropertyAccessorInterface $propertyAccessor)
    {
        $this->extendedMetadataFactory = $extendedMetadataFactory;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof TranslationInterface) {
                continue;
            }

            $translatable = $entity->getTranslatable();

            if (null === $translatable) {
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
