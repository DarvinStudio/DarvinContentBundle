<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\CanonicalUrl;

use Darvin\ContentBundle\EventListener\Pagination\PagerSubscriber;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Canonical URL generator
 */
class CanonicalUrlGenerator
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
     * @param string[]                                       $queryParamWhitelist Request query parameter name whitelist
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router, array $queryParamWhitelist)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;

        $this->queryParamWhitelist = [];

        foreach ($queryParamWhitelist as $name) {
            $this->addQueryParamToWhitelist($name);
        }
    }

    /**
     * @param string $name Request query parameter name
     */
    public function addQueryParamToWhitelist($name)
    {
        $pattern = $this->createPattern($name);
        $this->queryParamWhitelist[$pattern] = $pattern;
    }

    /**
     * @return string|null
     */
    public function generate()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (empty($request)) {
            return null;
        }

        $params = $request->query->all();

        if (empty($params) || !$request->attributes->has('_route')) {
            return $request->getUri();
        }

        $canonical = true;

        $whitelist = $this->queryParamWhitelist;

        foreach ($request->attributes->get(PagerSubscriber::REQUEST_ATTR_PAGE_PARAMS, []) as $name) {
            if (isset($params[$name]) && 1 === (int)$params[$name]) {
                continue;
            }

            $pattern = $this->createPattern($name);
            $whitelist[$pattern] = $pattern;
        }
        foreach ($params as $name => $value) {
            foreach ($whitelist as $pattern) {
                if (preg_match($pattern, $name)) {
                    continue 2;
                }
            }

            unset($params[$name]);

            $canonical = false;
        }
        if ($canonical) {
            return $request->getUri();
        }

        return $this->router->generate(
            $request->attributes->get('_route'),
            array_merge($request->attributes->get('_route_params', []), $params),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $text Text
     *
     * @return string
     */
    private function createPattern($text)
    {
        return '/^'.$text.'$/';
    }
}