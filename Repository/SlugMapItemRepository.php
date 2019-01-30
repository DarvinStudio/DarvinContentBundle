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
     * @param string[] $entityClasses Entity classes
     * @param mixed    $entityId      Entity ID
     * @param array    $properties    Slug properties
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem[]
     */
    public function getForSlugMapSubscriber(array $entityClasses, $entityId, array $properties = []): array
    {
        if (empty($entityClasses)) {
            return [];
        }

        $entityClasses = array_values(array_unique($entityClasses));

        $qb = $this->createDefaultBuilder()
            ->andWhere('o.objectId = :entity_id')
            ->setParameter('entity_id', $entityId);

        if (!empty($properties)) {
            $qb->andWhere($qb->expr()->in('o.property', $properties));
        }

        $orX = $qb->expr()->orX();

        foreach ($entityClasses as $i => $class) {
            $param = sprintf('entity_class_%d', $i);

            $orX->add(sprintf('o.objectClass = :%s', $param));

            $qb->setParameter($param, $class);
        }

        $qb->andWhere($orX);

        return $qb->getQuery()->getResult();
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
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }
}
