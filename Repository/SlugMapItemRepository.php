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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Slug map item entity repository
 */
class SlugMapItemRepository extends EntityRepository
{
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

        $qb = $this->createBuilderByClassesAndId($classes, $id);

        if (!empty($properties)) {
            $qb->andWhere($qb->expr()->in('o.property', $properties));
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

        return $this->createBuilderByClassesAndId($classes, $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug Slug
     *
     * @return array
     */
    public function getSimilarSlugs(string $slug): array
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
    public function getSimilarSlugsBuilder(string $slug): QueryBuilder
    {
        return $this->createDefaultBuilder()
            ->andWhere('o.slug LIKE :slug')
            ->setParameter('slug', $slug.'%');
    }

    /**
     * @param string[] $classes Object classes
     * @param mixed    $id      Object ID
     *
     * @return \Doctrine\ORM\QueryBuilder
     * @throws \InvalidArgumentException
     */
    private function createBuilderByClassesAndId(array $classes, $id): QueryBuilder
    {
        if (empty($classes)) {
            throw new \InvalidArgumentException('Array of object classes is empty.');
        }

        $classes = array_values(array_unique($classes));

        $qb = $this->createDefaultBuilder()
            ->andWhere('o.objectId = :object_id')
            ->setParameter('object_id', $id);

        $orX = $qb->expr()->orX();

        foreach ($classes as $i => $class) {
            $param = sprintf('object_class_%d', $i);

            $orX->add(sprintf('o.objectClass = :%s', $param));

            $qb->setParameter($param, $class);
        }

        $qb->andWhere($orX);

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }
}
