<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension\Locale;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Switch locale Twig extension
 */
class SwitchExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param string[] $locales Locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_locale_switcher', [$this, 'renderSwitcher'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig\Environment $twig     Twig
     * @param string            $template Template
     * @param array             $options  Options
     *
     * @return string
     */
    public function renderSwitcher(Environment $twig, string $template = '@DarvinContent/locale_switcher.html.twig', array $options = []): string
    {
        return $twig->render($template, array_merge([
            'locales' => $this->locales,
        ], $options));
    }
}
