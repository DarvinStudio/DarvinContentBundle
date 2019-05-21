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
     * {@inheritdoc}
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
     * @param \Twig\Environment $twig     Twig
     * @param string            $template Template
     *
     * @return string|null
     */
    public function renderCanonicalUrlTag(Environment $twig, string $template = '@DarvinContent/canonical_url.html.twig'): ?string
    {
        $url = $this->canonicalUrlGenerator->generateCanonicalUrl();

        if (empty($url)) {
            return null;
        }

        return $twig->render($template, [
            'url' => $url,
        ]);
    }
}
