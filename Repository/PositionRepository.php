<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Repository;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Position entity repository
 */
class PositionRepository extends EntityRepository
{
    /**
     * @param \Darvin\ContentBundle\Entity\SlugMapItem|null $slug        Slug
     * @param string|null                                   $tag         Tag
     * @param string                                        $objectClass Object class
     * @param string[]                                      $objectIds   Object IDs
     *
     * @return \Darvin\ContentBundle\Entity\Position[]
     */
    public function getForRepositioner(?SlugMapItem $slug, ?string $tag, string $objectClass, array $objectIds): array
    {
        if (empty($objectIds)) {
            return [];
        }

        $qb = $this->createDefaultBuilder();
        $this
            ->addSlugFilter($qb, $slug)
            ->addTagFilter($qb, $tag)
            ->addObjectClassFilter($qb, $objectClass)
            ->addObjectIdsFilter($qb, $objectIds);

        $positions = [];

        /** @var \Darvin\ContentBundle\Entity\Position $position */
        foreach ($qb->getQuery()->getResult() as $position) {
            $positions[$position->getObjectId()] = $position;
        }

        return $positions;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb          Query builder
     * @param string                     $objectClass Object class
     *
     * @return PositionRepository
     */
    private function addObjectClassFilter(QueryBuilder $qb, string $objectClass): PositionRepository
    {
        $qb->andWhere('o.objectClass = :object_class')->setParameter('object_class', $objectClass);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param string[]                   $objectIds Object IDs
     *
     * @return PositionRepository
     * @throws \InvalidArgumentException
     */
    private function addObjectIdsFilter(QueryBuilder $qb, array $objectIds): PositionRepository
    {
        if (empty($objectIds)) {
            throw new \InvalidArgumentException('Array of object IDs is empty.');
        }

        $qb->andWhere($qb->expr()->in('o.objectId', ':object_ids'))->setParameter('object_ids', $objectIds);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder                    $qb   Query builder
     * @param \Darvin\ContentBundle\Entity\SlugMapItem|null $slug Slug
     *
     * @return PositionRepository
     */
    private function addSlugFilter(QueryBuilder $qb, ?SlugMapItem $slug): PositionRepository
    {
        null !== $slug
            ? $qb->andWhere('o.slug = :slug')->setParameter('slug', $slug)
            : $qb->andWhere('o.slug IS NULL');

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb  Query builder
     * @param string|null                $tag Tag
     *
     * @return PositionRepository
     */
    private function addTagFilter(QueryBuilder $qb, ?string $tag): PositionRepository
    {
        null !== $tag
             ? $qb->andWhere('o.tag = :tag')->setParameter('tag', $tag)
             : $qb->andWhere('o.tag IS NULL');

        return $this;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')->addOrderBy('o.value');
    }
}
