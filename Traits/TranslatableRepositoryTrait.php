<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Translatable entity repository trait
 */
trait TranslatableRepositoryTrait
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param string                     $locale    Locale
     * @param bool                       $addSelect Whether to add select
     *
     * @return TranslatableRepositoryTrait
     */
    protected function joinTranslations(QueryBuilder $qb, $locale = null, $addSelect = true)
    {
        $qb->innerJoin('o.translations', 'translations');

        if (!empty($locale)) {
            $qb->andWhere('translations.locale = :locale')->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb->addSelect('translations');
        }

        return $this;
    }
}