<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Slug map item factory
 */
class SlugMapItemFactory
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
     * @param object                                             $object       Object
     * @param array                                              $slugsMeta    Slugs metadata
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $doctrineMeta Doctrine metadata
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     * @throws \Darvin\ContentBundle\Slug\SlugException
     */
    public function createItems($object, array $slugsMeta, ClassMetadata $doctrineMeta)
    {
        $items = array();

        if (empty($slugsMeta)) {
            return $items;
        }

        $objectClass = ClassUtils::getClass($object);

        foreach ($slugsMeta as $slugProperty => $params) {
            if (!$this->propertyAccessor->isReadable($object, $slugProperty)) {
                throw new SlugException(sprintf('Property "%s::$%s" is not readable.', $objectClass, $slugProperty));
            }

            $slug = $this->propertyAccessor->getValue($object, $slugProperty);

            $ids = $doctrineMeta->getIdentifierValues($object);

            $items[] = new SlugMapItem($slug, $objectClass, reset($ids), $slugProperty);
        }

        return $items;
    }
}
