<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
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
     * @param string|null                $locale    Locale
     * @param bool                       $addSelect Whether to add select
     *
     * @return self
     */
    protected function joinTranslations(QueryBuilder $qb, ?string $locale = null, bool $addSelect = true)
    {
        $qb->innerJoin('o.translations', 'translations');

        if (null !== $locale) {
            $qb->andWhere('translations.locale = :locale')->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb->addSelect('translations');
        }

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param string|null                $locale    Locale
     * @param bool                       $addSelect Whether to add select
     *
     * @return self
     */
    protected function leftJoinTranslations(QueryBuilder $qb, ?string $locale = null, bool $addSelect = true)
    {
        $qb->leftJoin('o.translations', 'translations');

        if (null !== $locale) {
            $qb
                ->andWhere($qb->expr()->orX(
                    'translations.locale IS NULL',
                    'translations.locale = :locale'
                ))
                ->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb->addSelect('translations');
        }

        return $this;
    }
}
