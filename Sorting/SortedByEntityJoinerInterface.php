<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

use Doctrine\ORM\QueryBuilder;

/**
 * Sorted by entity joiner
 */
interface SortedByEntityJoinerInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb                   Query builder
     * @param string                     $sortedByPropertyPath Sorted by property path
     * @param string                     $locale               Locale
     */
    public function joinEntity(QueryBuilder $qb, $sortedByPropertyPath, $locale);
}
