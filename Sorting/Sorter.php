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
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
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
    public function sort(iterable $objects, ?string $tag = null, ?string $slug = null): array
    {
        $objectArray = [];

        foreach ($objects as $key => $object) {
            $objectArray[$key] = $object;
        }

        $objects = $objectArray;

        if (empty($objects)) {
            return [];
        }
        if (null === $slug) {
            $request = $this->requestStack->getCurrentRequest();

            if (null !== $request) {
                $routeParams = $request->attributes->get('_route_params', []);

                if (isset($routeParams['slug'])) {
                    $slug = $routeParams['slug'];
                }
            }
        }

        /** @var \Darvin\ContentBundle\Entity\SlugMapItem|null $slugObject */
        $slugObject = $this->om->getRepository(SlugMapItem::class)->findOneBy(['slug' => $slug]);

        if (null === $slugObject) {
            return $objects;
        }

        $first = reset($objects);

        $class = ClassUtils::getClass($first);

        $meta = $this->om->getClassMetadata($class);
        $ids  = [];

        foreach ($objects as $object) {
            $identifierValues = $meta->getIdentifierValues($object);

            $ids[] = reset($identifierValues);
        }

        $positions = $this->getPositionRepository()->getPositions($slugObject, $tag, $class, $ids);

        $sorted = [];

        foreach ($positions as $id => $position) {
            foreach ($objects as $key => $object) {
                $identifierValues = $meta->getIdentifierValues($object);

                if ((string)reset($identifierValues) === (string)$id) {
                    $sorted[$key] = $object;

                    unset($objects[$key]);
                }
            }
        }

        return array_merge($sorted, $objects);
    }

    /**
     * @return \Darvin\ContentBundle\Repository\PositionRepository
     */
    private function getPositionRepository(): PositionRepository
    {
        return $this->om->getRepository(Position::class);
    }
}
