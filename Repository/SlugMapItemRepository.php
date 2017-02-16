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

use Darvin\ContentBundle\Entity\SlugMapItem;
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
    public function getByEntityBuilder($entityClass, $entityId, array $properties = [])
    {
        $qb = $this->createDefaultQueryBuilder()
            ->andWhere('o.objectClass = :entity_class')
            ->setParameter('entity_class', $entityClass)
            ->andWhere('o.objectId = :entity_id')
            ->setParameter('entity_id', $entityId);

        return !empty($properties) ? $qb->andWhere($qb->expr()->in('o.property', $properties)) : $qb;
    }

    /**
     * @param \Darvin\ContentBundle\Entity\SlugMapItem $slugMapItem Slug map item
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getChildrenBuilder(SlugMapItem $slugMapItem)
    {
        return $this->createDefaultQueryBuilder()
            ->andWhere('o.objectClass = :object_class')
            ->setParameter('object_class', $slugMapItem->getObjectClass())
            ->andWhere('o.property = :property')
            ->setParameter('property', $slugMapItem->getProperty())
            ->andWhere('o.slug LIKE :slug')
            ->setParameter('slug', $slugMapItem->getSlug().'/%');
    }

    /**
     * @param string $slug Slug
     *
     * @return array
     */
    public function getSimilarSlugs($slug)
    {
        return array_map(function (array $row) {
            return $row['slug'];
        }, $this->getSimilarSlugsBuilder($slug)->select('o.slug')->getQuery()->getArrayResult());
    }

    /**
     * @param string $slug Slug
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSimilarSlugsBuilder($slug)
    {
        return $this->createDefaultQueryBuilder()
            ->andWhere('o.slug LIKE :slug')
            ->setParameter('slug', $slug.'%');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
