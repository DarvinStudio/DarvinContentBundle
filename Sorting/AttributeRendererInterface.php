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
     * @param iterable    $target Target
     * @param array       $tags   Tags
     * @param string|null $slug   Slug
     * @param array       $attr   Attributes
     *
     * @return string
     */
    public function renderContainerAttr(iterable $target, array $tags = [], ?string $slug = null, array $attr = []): string;

    /**
     * @param object $object Object
     * @param array  $attr   Attributes
     *
     * @return string
     */
    public function renderItemAttr(object $object, array $attr = []): string;
}
