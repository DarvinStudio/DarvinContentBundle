<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Repository\SlugMapItemRepository;
use Darvin\Utils\Sluggable\SlugHandlerInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Unique slug handler
 */
class UniqueSlugHandler implements SlugHandlerInterface
{
    /**
     * @var array
     */
    private $similarSlugs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->similarSlugs = [];
    }

    /**
     * {@inheritDoc}
     */
    public function handle(string &$slug, string &$suffix, $entity, EntityManagerInterface $em): void
    {
        $originalSlug = $slug;

        $entityClass = ClassUtils::getClass($entity);
        $entityIds   = $em->getClassMetadata($entityClass)->getIdentifierValues($entity);
        $entityId    = !empty($entityIds) ? reset($entityIds) : null;

        $similarSlugs = $this->getSimilarSlugs($em, $slug);

        if (!$this->isSlugUnique($slug, $entityClass, $entityId, $similarSlugs)) {
            $index = 0;

            do {
                $index++;
                $slug = $originalSlug.'-'.$index;
            } while (!$this->isSlugUnique($slug, $entityClass, $entityId, $similarSlugs));

            $suffix .= '-'.$index;
        }

        $this->similarSlugs[$originalSlug][] = $this->similarSlugs[$slug][] = [
            'slug'        => $slug,
            'objectClass' => $entityClass,
            'objectId'    => $entityId,
        ];
    }

    /**
     * @param string $slug         Slug
     * @param string $entityClass  Entity class
     * @param mixed  $entityId     Entity ID
     * @param array  $similarSlugs Similar slugs
     *
     * @return bool
     */
    private function isSlugUnique(string $slug, string $entityClass, $entityId, array $similarSlugs): bool
    {
        if (empty($similarSlugs)) {
            return true;
        }
        foreach ($similarSlugs as $similar) {
            if ($slug !== $similar['slug']) {
                continue;
            }
            if (null === $entityId) {
                return false;
            }
            if (!($entityId == $similar['objectId']
                && ($entityClass === $similar['objectClass'] || in_array($similar['objectClass'], class_parents($entityClass))))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em   Entity manager
     * @param string                               $slug Slug
     *
     * @return array
     */
    private function getSimilarSlugs(EntityManagerInterface $em, string $slug): array
    {
        if (!isset($this->similarSlugs[$slug])) {
            $this->similarSlugs[$slug] = $this->getSlugMapItemRepository($em)->getSimilar($slug, AbstractQuery::HYDRATE_ARRAY);
        }

        return $this->similarSlugs[$slug];
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em Entity manager
     *
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(EntityManagerInterface $em): SlugMapItemRepository
    {
        return $em->getRepository(SlugMapItem::class);
    }
}
