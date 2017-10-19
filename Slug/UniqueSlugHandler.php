<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\Utils\Sluggable\SlugHandlerInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;

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
     * {@inheritdoc}
     */
    public function handle($entity, &$slug, &$suffix, EntityManager $em)
    {
        $originalSlug = $slug;

        $entityClass = ClassUtils::getClass($entity);
        $entityIds   = $em->getClassMetadata($entityClass)->getIdentifierValues($entity);
        $entityId    = reset($entityIds);

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
            'slug'         => $slug,
            'object_class' => $entityClass,
            'object_id'    => $entityId,
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
    private function isSlugUnique($slug, $entityClass, $entityId, array $similarSlugs)
    {
        if (empty($similarSlugs)) {
            return true;
        }
        foreach ($similarSlugs as $similar) {
            if ($slug !== $similar['slug']) {
                continue;
            }
            if (empty($entityId)) {
                return false;
            }
            if (!($entityId == $similar['object_id']
                && ($entityClass === $similar['object_class'] || in_array($similar['object_class'], class_parents($entityClass))))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em   Entity manager
     * @param string                      $slug Slug
     *
     * @return array
     */
    private function getSimilarSlugs(EntityManager $em, $slug)
    {
        if (!isset($this->similarSlugs[$slug])) {
            $this->similarSlugs[$slug] = $this->getSlugMapItemRepository($em)->getSimilarSlugs($slug);
        }

        return $this->similarSlugs[$slug];
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em Entity manager
     *
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(EntityManager $em)
    {
        return $em->getRepository(SlugMapItem::class);
    }
}
