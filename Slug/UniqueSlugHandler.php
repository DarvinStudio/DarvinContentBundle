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
     * {@inheritdoc}
     */
    public function handle($entity, &$slug, &$suffix, EntityManager $em)
    {
        $similarSlugs = $this->getSlugMapItemRepository($em)->getSimilarSlugs($slug);

        if (!isset($similarSlugs[$slug])) {
            return;
        }

        $entityClass = ClassUtils::getClass($entity);
        $entityId    = $entity->getId();

        foreach ($similarSlugs as $similarSlug => $params) {
            if ($params['objectClass'] === $entityClass && (int) $params['objectId'] === $entityId) {
                unset($similarSlugs[$similarSlug]);
            }
        }
        if (!isset($similarSlugs[$slug])) {
            return;
        }

        $originalSlug = $slug;
        $index = 0;

        do {
            $index++;
            $slug = $originalSlug.'-'.$index;
        } while (isset($similarSlugs[$slug]));

        $suffix .= '-'.$index;
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
