<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension\Widget;

use Darvin\ContentBundle\Widget\WidgetRegistryInterface;
use Darvin\Utils\Service\ServiceProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Widget registry Twig extension
 */
class RegistryExtension extends AbstractExtension
{
    /**
     * @var \Darvin\Utils\Service\ServiceProviderInterface
     */
    private $widgetRegistryProvider;

    /**
     * @param \Darvin\Utils\Service\ServiceProviderInterface $widgetRegistryProvider Widget registry service provider
     */
    public function __construct(ServiceProviderInterface $widgetRegistryProvider)
    {
        $this->widgetRegistryProvider = $widgetRegistryProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_widget_exists', [$this->getWidgetRegistry(), 'widgetExists']),
            new TwigFunction('content_widget_render', [$this, 'renderWidget'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param string $name Widget name
     *
     * @return string|null
     */
    public function renderWidget(string $name): ?string
    {
        return $this->getWidgetRegistry()->getWidget($name)->getContent();
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetRegistryInterface
     */
    private function getWidgetRegistry(): WidgetRegistryInterface
    {
        return $this->widgetRegistryProvider->getService();
    }
}
