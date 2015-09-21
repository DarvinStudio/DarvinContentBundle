<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
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
     * @param string                     $locale    Locale
     * @param string                     $joinAlias Join alias
     */
    public function joinTranslation(QueryBuilder $qb, $locale, $joinAlias = null);
}