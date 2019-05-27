<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
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
            $this->whitelistQueryParam($name, $enabled);
        }
    }

    /**
     * @param string $name    Request query parameter name
     * @param bool   $enabled Is enabled
     */
    public function whitelistQueryParam(string $name, bool $enabled = true): void
    {
        $pattern = $this->createPattern($name);

        if (!isset($this->queryParamWhitelist[$pattern])) {
            $this->queryParamWhitelist[$pattern] = $enabled;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateCanonicalUrl(): ?string
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

        foreach ($params as $name => $value) {
            foreach ($this->queryParamWhitelist as $pattern => $enabled) {
                if ($enabled && preg_match($pattern, $name)) {
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
    private function createPattern(string $text): string
    {
        return '/^'.$text.'$/';
    }
}
