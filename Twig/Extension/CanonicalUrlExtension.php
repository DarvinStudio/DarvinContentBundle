<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Canonical URL Twig extension
 */
class CanonicalUrlExtension extends AbstractExtension
{
    private const DEFAULT_TEMPLATE = '@DarvinContent/canonical_url.html.twig';

    /**
     * @var \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface
     */
    private $canonicalUrlGenerator;

    /**
     * @param \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface $canonicalUrlGenerator Canonical URL generator
     */
    public function __construct(CanonicalUrlGeneratorInterface $canonicalUrlGenerator)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_canonical_url', [$this, 'renderCanonicalUrlTag'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig\Environment $twig        Twig
     * @param string|null       $template    Template
     * @param string|null       $route       Route
     * @param array|null        $routeParams Route parameters
     *
     * @return string|null
     */
    public function renderCanonicalUrlTag(
        Environment $twig,
        ?string $template = null,
        ?string $route = null,
        ?array $routeParams = null
    ): ?string {
        $url = $this->canonicalUrlGenerator->generateCanonicalUrl($route, $routeParams);

        if (null === $url) {
            return null;
        }
        if (null === $template) {
            $template = self::DEFAULT_TEMPLATE;
        }

        return $twig->render($template, [
            'url' => $url,
        ]);
    }
}
