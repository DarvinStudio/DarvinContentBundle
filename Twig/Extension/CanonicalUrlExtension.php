<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGenerator;

/**
 * Canonical URL Twig extension
 */
class CanonicalUrlExtension extends \Twig_Extension
{
    /**
     * @var \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGenerator
     */
    private $canonicalUrlGenerator;

    /**
     * @param \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGenerator $canonicalUrlGenerator Canonical URL generator
     */
    public function __construct(CanonicalUrlGenerator $canonicalUrlGenerator)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('content_canonical_url', [$this, 'renderCanonicalUrlTag'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $env      Environment
     * @param string            $template Template
     *
     * @return string
     */
    public function renderCanonicalUrlTag(\Twig_Environment $env, $template = 'DarvinContentBundle::canonical_url.html.twig')
    {
        $url = $this->canonicalUrlGenerator->generate();

        if (empty($url)) {
            return null;
        }

        return $env->render($template, [
            'url' => $url,
        ]);
    }
}
