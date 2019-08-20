<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

use Darvin\ContentBundle\Entity\Position;
use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Repository\PositionRepository;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Sorter
 */
class Sorter implements SorterInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager     $om           Object manager
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     */
    public function __construct(ObjectManager $om, RequestStack $requestStack)
    {
        $this->om = $om;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function addOrderByClause(QueryBuilder $qb, array $tags = [], ?string $slug = null): QueryBuilder
    {
        if (null === $slug) {
            $slug = $this->getSlugFromRequest();
        }

        $classes = $qb->getRootEntities();

        $sortedIds = $this->getPositionRepository()->getObjectIdsForSorter($this->om->getRepository(SlugMapItem::class)->findOneBy(['slug' => $slug]), $tags, reset($classes));

        if (!empty($sortedIds)) {
            $aliases     = $qb->getRootAliases();
            $identifiers = $this->om->getClassMetadata(reset($classes))->getIdentifier();

            $qb->orderBy(sprintf('FIELD(%s.%s, %s)', reset($aliases), reset($identifiers), implode(', ', $sortedIds)));
        }

        return $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function sort(iterable $objects, array $tags = [], ?string $slug = null): array
    {
        $offset = null;

        if ($objects instanceof AbstractPagination && $objects->getCurrentPageNumber() > 1) {
            $offset = $objects->getItemNumberPerPage() * ($objects->getCurrentPageNumber() - 1);
        }

        $objects = $this->objectsToArray($objects);

        if (empty($objects)) {
            return [];
        }
        if (null === $slug) {
            $slug = $this->getSlugFromRequest();
        }

        $class = ClassUtils::getClass(reset($objects));

        $count         = count($objects);
        $keys          = array_keys($objects);
        $meta          = $this->om->getClassMetadata($class);
        $sortedIds     = $this->getPositionRepository()->getObjectIdsForSorter($this->om->getRepository(SlugMapItem::class)->findOneBy(['slug' => $slug]), $tags, $class);
        $sortedObjects = [];

        for ($i = 0; $i < $count; $i++) {
            $position = $i + (int)$offset;

            if (!isset($sortedIds[$position])) {
                $key = $keys[$i];

                if (isset($objects[$key])) {
                    $sortedObjects[$key] = $objects[$key];

                    unset($objects[$key]);
                }

                continue;
            }
            foreach ($objects as $key => $object) {
                if ((string)$this->getObjectId($object, $meta) === $sortedIds[$position]) {
                    $sortedObjects[$key] = $object;

                    unset($objects[$key]);
                }
            }

            unset($sortedIds[$position]);
        }
        foreach ($sortedIds as $sortedId) {
            foreach ($objects as $key => $object) {
                if ((string)$this->getObjectId($object, $meta) === $sortedId) {
                    $sortedObjects[$key] = $object;

                    unset($objects[$key]);
                }
            }
        }

        return $sortedObjects + $objects;
    }

    /**
     * @param object                                             $object Object
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $meta   Metadata
     *
     * @return mixed
     */
    private function getObjectId($object, ClassMetadata $meta)
    {
        $ids = $meta->getIdentifierValues($object);

        return reset($ids);
    }

    /**
     * @return string|null
     */
    private function getSlugFromRequest(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $params = $request->attributes->get('_route_params', []);

        return $params['slug'] ?? null;
    }

    /**
     * @param iterable $objects Objects
     *
     * @return array
     */
    private function objectsToArray(iterable $objects): array
    {
        $array = [];

        foreach ($objects as $key => $object) {
            $array[$key] = $object;
        }

        return $array;
    }

    /**
     * @return \Darvin\ContentBundle\Repository\PositionRepository
     */
    private function getPositionRepository(): PositionRepository
    {
        return $this->om->getRepository(Position::class);
    }
}
