<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Darvin\Utils\Sluggable\SluggableManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Slug rebuilder
 */
class SlugRebuilder implements SlugRebuilderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Darvin\Utils\Sluggable\SluggableManagerInterface
     */
    private $sluggableManager;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                        $em               Entity manager
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface              $metadataFactory  Extended metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     * @param \Darvin\Utils\Sluggable\SluggableManagerInterface           $sluggableManager Sluggable manager
     */
    public function __construct(
        EntityManagerInterface $em,
        MetadataFactoryInterface $metadataFactory,
        PropertyAccessorInterface $propertyAccessor,
        SluggableManagerInterface $sluggableManager
    ) {
        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
        $this->propertyAccessor = $propertyAccessor;
        $this->sluggableManager = $sluggableManager;
    }

    /**
     * {@inheritDoc}
     */
    public function rebuildSlugs(?callable $output = null): void
    {
        if (null === $output) {
            $output = function ($message): void {
            };
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->getReflectionClass()->isAbstract() || !$this->sluggableManager->isSluggable($meta->getName())) {
                continue;
            }

            $properties = array_keys($this->metadataFactory->getExtendedMetadata($meta->getName())['slugs']);

            foreach ($this->em->getRepository($meta->getName())->findAll() as $entity) {
                $comments = [];

                foreach ($properties as $property) {
                    // Reset slug
                    $this->propertyAccessor->setValue($entity, $property, uniqid((string)mt_rand(), true));
                }

                $this->em->flush();

                foreach ($properties as $property) {
                    $comments[] = $property.': '.$this->propertyAccessor->getValue($entity, $property);
                }

                $ids = $meta->getIdentifierValues($entity);

                $output(implode(' ', [$meta->getName(), reset($ids), implode(', ', $comments)]));
            }
        }

        $this->em->flush();
    }
}
