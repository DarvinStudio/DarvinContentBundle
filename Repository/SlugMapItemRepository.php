<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Slug map item entity repository
 */
class SlugMapItemRepository extends EntityRepository
{
    /**
     * @param string $entityClass Entity class
     * @param mixed  $entityId    Entity ID
     * @param array  $properties  Slug properties
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByEntityBuilder($entityClass, $entityId, array $properties = array())
    {
        $qb = $this->createDefaultQueryBuilder()
            ->andWhere('o.entityClass = :entity_class')
            ->setParameter('entity_class', $entityClass)
            ->andWhere('o.entityId = :entity_id')
            ->setParameter('entity_id', $entityId);

        return !empty($properties) ? $qb->andWhere($qb->expr()->in('o.property', $properties)) : $qb;
    }

    /**
     * @param string $slug Slug
     *
     * @return array
     */
    public function getSimilarSlugs($slug)
    {
        $rows = $this->createDefaultQueryBuilder()
            ->select('o.slug')
            ->andWhere('o.slug LIKE :slug')
            ->setParameter('slug', $slug.'%')
            ->getQuery()
            ->getArrayResult();

        return array_map(function (array $row) {
            return $row['slug'];
        }, $rows);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
