<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

use Darvin\ContentBundle\Entity\ContentReference;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Content reference factory
 */
class ContentReferenceFactory implements ContentReferenceFactoryInterface
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritDoc}
     */
    public function createContentReferences($object, array $slugMeta, ClassMetadata $doctrineMeta): array
    {
        if (empty($slugMeta)) {
            return [];
        }

        $references  = [];
        $objectClass = ClassUtils::getClass($object);

        foreach (array_keys($slugMeta) as $property) {
            if (!$this->propertyAccessor->isReadable($object, $property)) {
                throw new \LogicException(sprintf('Property "%s::$%s" is not readable.', $objectClass, $property));
            }

            $objectIds = $doctrineMeta->getIdentifierValues($object);

            $references[] = new ContentReference(
                $this->propertyAccessor->getValue($object, $property),
                $objectClass,
                reset($objectIds),
                $property
            );
        }

        return $references;
    }
}
