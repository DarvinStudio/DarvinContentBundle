<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\CanonicalUrl;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Canonical URL generator
 */
class CanonicalUrlGenerator implements CanonicalUrlGeneratorInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $queryParamWhitelist;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack        Request stack
     * @param \Symfony\Component\Routing\RouterInterface     $router              Router
     * @param array                                          $queryParamWhitelist Request query parameter name whitelist
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router, array $queryParamWhitelist)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;

        $this->queryParamWhitelist = [];

        foreach ($queryParamWhitelist as $name => $enabled) {
            $pattern = sprintf('/^%s$/', $name);

            if (!isset($this->queryParamWhitelist[$pattern])) {
                $this->queryParamWhitelist[$pattern] = $enabled;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateCanonicalUrl(?string $route = null, ?array $routeParams = null): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }
        if (null === $route) {
            $route = $request->attributes->get('_route');
        }
        if (null === $route) {
            return $request->getUri();
        }

        $params = $request->query->all();

        foreach ($params as $name => $value) {
            foreach ($this->queryParamWhitelist as $pattern => $enabled) {
                if ($enabled && preg_match($pattern, $name)) {
                    continue 2;
                }
            }

            unset($params[$name]);
        }

        return $this->router->generate(
            $route,
            array_merge(null !== $routeParams ? $routeParams : $request->attributes->get('_route_params', []), $params),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
