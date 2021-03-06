<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Form\DataTransformer\Admin;

use Darvin\AdminBundle\EntityNamer\EntityNamerInterface;
use Darvin\ContentBundle\Entity\ContentReference;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Content reference to array admin form data transformer
 */
class ContentReferenceToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var \Darvin\AdminBundle\EntityNamer\EntityNamerInterface
     */
    private $entityNamer;

    /**
     * @param \Darvin\AdminBundle\EntityNamer\EntityNamerInterface $entityNamer Entity namer
     */
    public function __construct(EntityNamerInterface $entityNamer)
    {
        $this->entityNamer = $entityNamer;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof ContentReference) {
            throw new TransformationFailedException(sprintf('value must be instance of "%s".', ContentReference::class));
        }

        $classProperty = implode('_', [$this->entityNamer->name($value->getObjectClass()), $value->getProperty()]);

        return [
            'class_property' => $classProperty,
            $classProperty   => $value,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value): ?ContentReference
    {
        $classProperty = $value['class_property'];

        return null !== $classProperty ? $value[$classProperty] : null;
    }
}
