<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Filterer;

use Doctrine\ORM\QueryBuilder;

/**
 * Filterer
 */
interface FiltererInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb          Query builder
     * @param array|null                 $filterData  Filter data
     * @param array                      $options     Options
     * @param bool                       $conjunction Whether to use conjunction (otherwise - disjunction)
     *
     * @throws \InvalidArgumentException
     */
    public function filter(QueryBuilder $qb, ?array $filterData = null, array $options = [], bool $conjunction = true): void;
}
