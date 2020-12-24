<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Router;

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\Utils\Homepage\HomepageRouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Content reference router
 */
class ContentReferenceRouter implements ContentReferenceRouterInterface
{
    private const CONTENT_ROUTE = 'darvin_content_show';

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $genericRouter;

    /**
     * @var \Darvin\Utils\Homepage\HomepageRouterInterface
     */
    private $homepageRouter;

    /**
     * @param \Symfony\Component\Routing\RouterInterface     $genericRouter  Generic router
     * @param \Darvin\Utils\Homepage\HomepageRouterInterface $homepageRouter Homepage router
     */
    public function __construct(RouterInterface $genericRouter, HomepageRouterInterface $homepageRouter)
    {
        $this->genericRouter = $genericRouter;
        $this->homepageRouter = $homepageRouter;
    }

    /**
     * {@inheritDoc}
     */
    public function generateUrl(?ContentReference $contentReference, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $parameters = []): ?string
    {
        if (null === $contentReference) {
            return null;
        }
        if ($this->homepageRouter->isHomepage($contentReference->getObject())) {
            return $this->homepageRouter->generate($referenceType, $parameters);
        }

        return $this->genericRouter->generate(self::CONTENT_ROUTE, array_merge($parameters, ['slug' => $contentReference->getSlug()]), $referenceType);
    }
}
