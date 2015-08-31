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
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.entityClass = :entity_class')
            ->setParameter('entity_class', $entityClass)
            ->andWhere('o.entityId = :entity_id')
            ->setParameter('entity_id', $entityId);

        return !empty($properties) ? $qb->andWhere($qb->expr()->in('o.property', $properties)) : $qb;
    }
}
