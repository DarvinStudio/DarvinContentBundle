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
use Doctrine\ORM\EntityManager;

/**
 * Unique slug handler
 */
class UniqueSlugHandler implements SlugHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(&$slug, &$suffix, EntityManager $em)
    {
        $similarSlugs = $this->getSlugMapItemRepository($em)->getSimilarSlugs($slug);

        if (empty($similarSlugs) || !in_array($slug, $similarSlugs)) {
            return;
        }

        $originalSlug = $slug;
        $index = 0;

        do {
            $index++;
            $slug = $originalSlug.'-'.$index;
        } while (in_array($slug, $similarSlugs));

        $suffix.='-'.$index;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em Entity manager
     *
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(EntityManager $em)
    {
        return $em->getRepository(SlugMapItem::SLUG_MAP_ITEM_CLASS);
    }
}
