<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Repository;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Slug map item entity repository
 */
class SlugMapItemRepository extends EntityRepository
{
    /**
     * @param string[] $classes  Object classes
     * @param string   $property Property
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createBuilderByClassesAndProperty(array $classes, string $property): QueryBuilder
    {
        $qb = $this->createDefaultBuilder();
        $this
            ->addObjectClassesFilter($qb, $classes)
            ->addPropertyFilter($qb, $property);

        return $qb;
    }

    /**
     * @param string[] $slugs          Slugs
     * @param string[] $classBlacklist Object class blacklist
     *
     * @return array Key - slug, value - array of child slug map item entities
     */
    public function getChildrenBySlugs(array $slugs, array $classBlacklist = []): array
    {
        if (empty($slugs)) {
            return [];
        }

        $slugs          = array_values(array_unique($slugs));
        $classBlacklist = array_unique($classBlacklist);

        $qb = $this->createDefaultBuilder();

        $orX = $qb->expr()->orX();

        foreach ($slugs as $i => $slug) {
            $param = sprintf('slug_%d', $i);

            $orX->add('o.slug LIKE :'.$param);

            $qb->setParameter($param, $slug.'%');
        }

        $qb->andWhere($orX);

        if (!empty($classBlacklist)) {
            $qb
                ->andWhere($qb->expr()->notIn('o.objectClass', ':class_blacklist'))
                ->setParameter('class_blacklist', $classBlacklist);
        }

        $children = array_fill_keys($slugs, []);

        /** @var \Darvin\ContentBundle\Entity\SlugMapItem $slugMapItem */
        foreach ($qb->getQuery()->getResult() as $slugMapItem) {
            foreach ($slugs as $slug) {
                if (0 === strpos($slugMapItem->getSlug(), $slug)) {
                    $children[$slug][] = $slugMapItem;
                }
            }
        }

        return $children;
    }

    /**
     * @param string[] $classes    Object classes
     * @param mixed    $id         Object ID
     * @param array    $properties Slug properties
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     */
    public function getForSlugMapSubscriber(array $classes, $id, array $properties = []): array
    {
        if (empty($classes)) {
            return [];
        }

        $qb = $this->createDefaultBuilder();
        $this
            ->addObjectClassesFilter($qb, $classes)
            ->addObjectIdFilter($qb, $id);

        if (!empty($properties)) {
            $this->addPropertiesFilter($qb, $properties);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string[] $classes Object classes
     * @param mixed    $id      Object ID
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem|null
     */
    public function getOneByClassesAndId(array $classes, $id): ?SlugMapItem
    {
        if (empty($classes)) {
            return null;
        }

        $qb = $this->createDefaultBuilder();
        $this
            ->addObjectClassesFilter($qb, $classes)
            ->addObjectIdFilter($qb, $id);

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug Slug
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     */
    public function getParentsBySlug(string $slug): array
    {
        return $this->createDefaultBuilder()
            ->andWhere(':slug LIKE CONCAT(o.slug, \'%\')')
            ->andWhere('o.slug != :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->enableResultCache()
            ->getResult();
    }

    /**
     * @param string $slug          Slug
     * @param int    $hydrationMode Result hydration mode
     *
     * @return array
     */
    public function getSimilar(string $slug, int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array
    {
        $qb = $this->createDefaultBuilder();
        $this->addSimilarSlugsFilter($qb, $slug);

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb      Query builder
     * @param string[]                   $classes Object classes
     *
     * @return SlugMapItemRepository
     * @throws \InvalidArgumentException
     */
    private function addObjectClassesFilter(QueryBuilder $qb, array $classes): SlugMapItemRepository
    {
        if (empty($classes)) {
            throw new \InvalidArgumentException('Array of object classes is empty.');
        }

        $classes = array_values(array_unique($classes));

        $orX = $qb->expr()->orX();

        foreach ($classes as $i => $class) {
            $param = sprintf('object_class_%d', $i);

            $orX->add(sprintf('o.objectClass = :%s', $param));

            $qb->setParameter($param, $class);
        }

        $qb->andWhere($orX);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     * @param mixed                      $id Object ID
     *
     * @return SlugMapItemRepository
     */
    private function addObjectIdFilter(QueryBuilder $qb, $id): SlugMapItemRepository
    {
        $qb->andWhere('o.objectId = :object_id')->setParameter('object_id', $id);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb         Query builder
     * @param string[]                   $properties Properties
     *
     * @return SlugMapItemRepository
     * @throws \InvalidArgumentException
     */
    private function addPropertiesFilter(QueryBuilder $qb, array $properties): SlugMapItemRepository
    {
        if (empty($properties)) {
            throw new \InvalidArgumentException('Array of properties is empty.');
        }

        $qb->andWhere($qb->expr()->in('o.property', $properties));

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb       Query builder
     * @param string                     $property Property
     *
     * @return SlugMapItemRepository
     */
    private function addPropertyFilter(QueryBuilder $qb, string $property): SlugMapItemRepository
    {
        $qb->andWhere('o.property = :property')->setParameter('property', $property);

        return $this;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb   Query builder
     * @param string                     $slug Slug
     *
     * @return SlugMapItemRepository
     */
    private function addSimilarSlugsFilter(QueryBuilder $qb, string $slug): SlugMapItemRepository
    {
        $qb->andWhere('o.slug LIKE :slug')->setParameter('slug', $slug.'%');

        return $this;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }
}
