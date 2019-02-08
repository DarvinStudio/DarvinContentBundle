<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\Utils\Homepage\HomepageRouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Slug map router
 */
class SlugMapRouter implements SlugMapRouterInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $genericRouter;

    /**
     * @var \Darvin\Utils\Homepage\HomepageRouterInterface
     */
    private $homepageRouter;

    /**
     * @var string
     */
    private $contentRoute;

    /**
     * @param \Symfony\Component\Routing\RouterInterface     $genericRouter  Generic router
     * @param \Darvin\Utils\Homepage\HomepageRouterInterface $homepageRouter Homepage router
     * @param string                                         $contentRoute   Content route name
     */
    public function __construct(RouterInterface $genericRouter, HomepageRouterInterface $homepageRouter, string $contentRoute)
    {
        $this->genericRouter = $genericRouter;
        $this->homepageRouter = $homepageRouter;
        $this->contentRoute = $contentRoute;
    }

    /**
     * {@inheritDoc}
     */
    public function generateUrl(?SlugMapItem $item, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $params = []): ?string
    {
        if (empty($item)) {
            return null;
        }
        if ($this->homepageRouter->isHomepage($item->getObject())) {
            return $this->homepageRouter->generate($referenceType, $params);
        }

        return $this->genericRouter->generate($this->contentRoute, array_merge($params, ['slug' => $item->getSlug()]), $referenceType);
    }
}
