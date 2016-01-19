<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Metadata entity repository trait
 */
trait MetadataEntityRepositoryTrait
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $alias Alias
     *
     * @return MetadataEntityRepositoryTrait
     */
    public function addNotHiddenFilter(QueryBuilder $qb, $alias = 'o')
    {
        $qb->andWhere($alias.'.hidden = :hidden')->setParameter('hidden', false);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $alias Alias
     *
     * @return MetadataEntityRepositoryTrait
     */
    protected function addEnabledFilter(QueryBuilder $qb, $alias = 'o')
    {
        $qb->andWhere($alias.'.enabled = :enabled')->setParameter('enabled', true);

        return $this;
    }
}
