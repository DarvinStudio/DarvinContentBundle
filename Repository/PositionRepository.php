<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Repository;

use Darvin\ContentBundle\Entity\ContentReference;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Position entity repository
 */
class PositionRepository extends EntityRepository
{
    /**
     * @param \Darvin\ContentBundle\Entity\ContentReference|null $contentReference Content reference
     * @param array                                              $tags             Tags
     * @param string[]                                           $objectClasses    Object classes
     * @param string[]                                           $objectIds        Object IDs
     *
     * @return \Darvin\ContentBundle\Entity\Position[]
     */
    public function getForRepositioner(?ContentReference $contentReference, array $tags, array $objectClasses, array $objectIds): array
    {
        if (empty($objectIds)) {
            return [];
        }

        $qb = $this->createDefaultBuilder();
        $this
            ->addContentReferenceFilter($qb, $contentReference)
            ->addTagsFilter($qb, $tags)
            ->addObjectClassesFilter($qb, $objectClasses)
            ->addObjectIdsFilter($qb, $objectIds);

        $positions = [];

        /** @var \Darvin\ContentBundle\Entity\Position $position */
        foreach ($qb->getQuery()->getResult() as $position) {
            $positions[$position->getObjectId()] = $position;
        }

        return $positions;
    }

    /**
     * @param \Darvin\ContentBundle\Entity\ContentReference|null $contentReference Content reference
     * @param array                                              $tags             Tags
     * @param string[]                                           $objectClasses    Object classes
     *
     * @return array
     */
    public function getObjectIdsForSorter(?ContentReference $contentReference, array $tags, array $objectClasses): array
    {
        $qb = $this->createDefaultBuilder()
            ->select('o.objectId');
        $this
            ->addContentReferenceFilter($qb, $contentReference)
            ->addTagsFilter($qb, $tags)
            ->addObjectClassesFilter($qb, $objectClasses);

        return array_column($qb->getQuery()->getScalarResult(), 'objectId');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder                         $qb               Query builder
     * @param \Darvin\ContentBundle\Entity\ContentReference|null $contentReference Content reference
     *
     * @return PositionRepository
     */
    private function addContentReferenceFilter(QueryBuilder $qb, ?ContentReference $contentReference): PositionRepository
    {
        null !== $contentReference
            ? $qb->andWhere('o.contentReference = :content_reference')->setParameter('content_reference', $contentReference)
            : $qb->andWhere('o.contentReference IS NULL');

        return $this;
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
     * @param \Doctrine\ORM\QueryBuilder $qb            Query builder
     * @param string[]                   $objectClasses Object classes
     *
     * @return PositionRepository
     * @throws \InvalidArgumentException
     */
    private function addObjectClassesFilter(QueryBuilder $qb, array $objectClasses): PositionRepository
    {
        if (empty($objectClasses)) {
            throw new \InvalidArgumentException('Array of object classes is empty.');
        }

        $objectClasses = array_unique($objectClasses);

        if (1 === count($objectClasses)) {
            return $this->addObjectClassFilter($qb, reset($objectClasses));
        }

        $qb->andWhere($qb->expr()->in('o.objectClass', ':object_classes'))->setParameter('object_classes', $objectClasses);

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
     * @param \Doctrine\ORM\QueryBuilder $qb   Query builder
     * @param array                      $tags Tags
     *
     * @return PositionRepository
     */
    private function addTagsFilter(QueryBuilder $qb, array $tags): PositionRepository
    {
        $qb->andWhere('o.tags = :tags')->setParameter('tags', serialize($tags));

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
