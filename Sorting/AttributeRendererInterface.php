<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

/**
 * Sorting attribute renderer
 */
interface AttributeRendererInterface
{
    /**
     * @param iterable    $objects Objects
     * @param string|null $tag     Tag
     * @param string|null $slug    Slug
     * @param array       $attr    Attributes
     *
     * @return string
     */
    public function renderContainerAttr(iterable $objects, ?string $tag = null, ?string $slug = null, array $attr = []): string;

    /**
     * @param object $object Object
     * @param array  $attr   Attributes
     *
     * @return string
     */
    public function renderItemAttr($object, array $attr = []): string;
}
