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
    public function getByEntityBuilder($entityClass, $entityId, array $properties = [])
    {
        $qb = $this->createDefaultBuilder()
            ->andWhere('o.objectClass = :entity_class')
            ->setParameter('entity_class', $entityClass)
            ->andWhere('o.objectId = :entity_id')
            ->setParameter('entity_id', $entityId);

        return !empty($properties) ? $qb->andWhere($qb->expr()->in('o.property', $properties)) : $qb;
    }

    /**
     * @param string[] $slugs          Slugs
     * @param string[] $classBlacklist Object class blacklist
     *
     * @return array Key - slug, value - array of child slug map item entities
     */
    public function getBySlugsChildren(array $slugs, array $classBlacklist = [])
    {
        if (empty($slugs)) {
            return [];
        }

        $qb = $this->createDefaultBuilder();
        $orX = $qb->expr()->orX();

        foreach ($slugs as $key => $slug) {
            $param = 'slug_'.$key;
            $orX->add('o.slug LIKE :'.$param);
            $qb->setParameter($param, $slug.'%');
        }
        if (!empty($classBlacklist)) {
            $qb
                ->andWhere($qb->expr()->notIn('o.objectClass', ':class_blacklist'))
                ->setParameter('class_blacklist', $classBlacklist);
        }

        $children = array_fill_keys($slugs, []);

        /** @var \Darvin\ContentBundle\Entity\SlugMapItem $slugMapItem */
        foreach ($qb->andWhere($orX)->getQuery()->getResult() as $slugMapItem) {
            foreach ($slugs as $slug) {
                if (0 === strpos($slugMapItem->getSlug(), $slug)) {
                    $children[$slug][] = $slugMapItem;
                }
            }
        }

        return $children;
    }

    /**
     * @param string $slug Slug
     *
     * @return array
     */
    public function getSimilarSlugs($slug)
    {
        return $this->getSimilarSlugsBuilder($slug)
            ->select('o.slug')
            ->addSelect('o.objectClass object_class')
            ->addSelect('o.objectId object_id')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * @param string $slug Slug
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSimilarSlugsBuilder($slug)
    {
        return $this->createDefaultBuilder()
            ->andWhere('o.slug LIKE :slug')
            ->setParameter('slug', $slug.'%');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
