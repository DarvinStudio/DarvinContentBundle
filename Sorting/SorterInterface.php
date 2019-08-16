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
 * Sorter
 */
interface SorterInterface
{
    /**
     * @param iterable    $objects Objects
     * @param string|null $tag     Tag
     * @param string|null $slug    Slug
     *
     * @return array
     */
    public function sort(iterable $objects, ?string $tag = null, ?string $slug = null): array;
}
