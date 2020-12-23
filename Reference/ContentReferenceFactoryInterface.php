<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

use Doctrine\Persistence\Mapping\ClassMetadata;

/**
 * Content reference factory
 */
interface ContentReferenceFactoryInterface
{
    /**
     * @param object                                      $object       Object
     * @param array                                       $slugMeta     Slug metadata
     * @param \Doctrine\Persistence\Mapping\ClassMetadata $doctrineMeta Doctrine metadata
     *
     * @return \Darvin\ContentBundle\Entity\ContentReference[]
     * @throws \LogicException
     */
    public function createContentReferences(object $object, array $slugMeta, ClassMetadata $doctrineMeta): array;
}
