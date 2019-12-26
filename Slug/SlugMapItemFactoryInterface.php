<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Doctrine\Persistence\Mapping\ClassMetadata;

/**
 * Slug map item factory
 */
interface SlugMapItemFactoryInterface
{
    /**
     * @param object                                      $object       Object
     * @param array                                       $slugsMeta    Slugs metadata
     * @param \Doctrine\Persistence\Mapping\ClassMetadata $doctrineMeta Doctrine metadata
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     * @throws \LogicException
     */
    public function createItems(object $object, array $slugsMeta, ClassMetadata $doctrineMeta): array;
}
