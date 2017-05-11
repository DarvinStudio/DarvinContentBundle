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
    public function handle(&$slug, &$suffix, EntityManager $em)
    {
        $originalSlug = $slug;

        $similarSlugs = $this->getSimilarSlugs($originalSlug, $em);

        if (isset($similarSlugs[$slug])) {
            $index = 0;

            do {
                $index++;
                $slug = $originalSlug.'-'.$index;
            } while (isset($similarSlugs[$slug]));

            $suffix .= '-'.$index;
        }

        $this->similarSlugs[$originalSlug][$slug] = $slug;
    }

    /**
     * @param string                      $originalSlug Original slug
     * @param \Doctrine\ORM\EntityManager $em           Entity manager
     *
     * @return string[]
     */
    private function getSimilarSlugs($originalSlug, EntityManager $em)
    {
        if (!isset($this->similarSlugs[$originalSlug])) {
            $similarSlugs = $this->getSlugMapItemRepository($em)->getSimilarSlugs($originalSlug);

            $this->similarSlugs[$originalSlug] = array_combine($similarSlugs, $similarSlugs);
        }

        return $this->similarSlugs[$originalSlug];
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
