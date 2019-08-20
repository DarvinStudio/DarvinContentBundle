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

use Doctrine\ORM\QueryBuilder;

/**
 * Sorter
 */
interface SorterInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb   Query builder
     * @param array                      $tags Tags
     * @param string|null                $slug Slug
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addOrderByClause(QueryBuilder $qb, array $tags = [], ?string $slug = null): QueryBuilder;

    /**
     * @param iterable    $objects Objects
     * @param array       $tags    Tags
     * @param string|null $slug    Slug
     *
     * @return array
     */
    public function sort(iterable $objects, array $tags = [], ?string $slug = null): array;
}
