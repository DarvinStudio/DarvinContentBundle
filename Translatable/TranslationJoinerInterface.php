<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Doctrine\ORM\QueryBuilder;

/**
 * Translation joiner
 */
interface TranslationJoinerInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param bool                       $addSelect Whether to add select
     * @param string|null                $locale    Locale
     * @param string|null                $joinAlias Join alias
     * @param bool                       $inner     Whether to use inner join instead of left (default)
     *
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function joinTranslation(QueryBuilder $qb, bool $addSelect = false, ?string $locale = null, ?string $joinAlias = null, bool $inner = false): void;
}
