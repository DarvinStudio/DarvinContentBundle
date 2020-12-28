<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * Sort entity joiner
 */
interface SortEntityJoinerInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb               Query builder
     * @param string|null                $sortPropertyPath Sort property path
     * @param string                     $locale           Locale
     *
     * @throws \InvalidArgumentException
     */
    public function joinEntity(QueryBuilder $qb, ?string $sortPropertyPath, string $locale): void;
}
