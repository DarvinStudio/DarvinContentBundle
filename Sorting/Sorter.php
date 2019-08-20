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
        $classes = $qb->getRootEntities();

        $class = reset($classes);

        $ids = $this->getPositionRepository()->getObjectIdsForSorter($this->getSlugObject($slug), $tags, $class);

        if (!empty($ids)) {
            $aliases     = $qb->getRootAliases();
            $identifiers = $qb->getEntityManager()->getClassMetadata($class)->getIdentifier();

            $qb->orderBy(sprintf('FIELD(%s.%s, %s)', reset($aliases), reset($identifiers), implode(', ', $ids)));
        }

        return $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function sort(iterable $objects, array $tags = [], ?string $slug = null): array
    {
        $objects = $this->objectsToArray($objects);

        if (empty($objects)) {
            return [];
        }

        $class = ClassUtils::getClass(reset($objects));

        $meta   = $this->om->getClassMetadata($class);
        $sorted = [];

        foreach ($this->getPositionRepository()->getObjectIdsForSorter($this->getSlugObject($slug), $tags, $class) as $id) {
            foreach ($objects as $key => $object) {
                if ((string)$this->getObjectId($object, $meta) === $id) {
                    $sorted[$key] = $object;

                    unset($objects[$key]);
                }
            }
        }

        return $objects + $sorted;
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
     * @param string|null $slug Slug string
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem|null
     */
    private function getSlugObject(?string $slug): ?SlugMapItem
    {
        if (null === $slug) {
            $request = $this->requestStack->getCurrentRequest();

            if (null !== $request) {
                $params = $request->attributes->get('_route_params', []);

                if (isset($params['slug'])) {
                    $slug = $params['slug'];
                }
            }
        }
        if (null !== $slug) {
            return $this->om->getRepository(SlugMapItem::class)->findOneBy(['slug' => $slug]);
        }

        return null;
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
