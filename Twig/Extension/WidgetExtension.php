<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\ContentBundle\Widget\WidgetEmbedderInterface;
use Darvin\ContentBundle\Widget\WidgetPoolInterface;
use Darvin\Utils\Service\ServiceProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Widget Twig extension
 */
class WidgetExtension extends AbstractExtension
{
    /**
     * @var \Darvin\Utils\Service\ServiceProviderInterface
     */
    private $widgetEmbedderProvider;

    /**
     * @var \Darvin\Utils\Service\ServiceProviderInterface
     */
    private $widgetPoolProvider;

    /**
     * @param \Darvin\Utils\Service\ServiceProviderInterface $widgetEmbedderProvider Widget embedder service provider
     * @param \Darvin\Utils\Service\ServiceProviderInterface $widgetPoolProvider     Widget pool service provider
     */
    public function __construct(ServiceProviderInterface $widgetEmbedderProvider, ServiceProviderInterface $widgetPoolProvider)
    {
        $this->widgetEmbedderProvider = $widgetEmbedderProvider;
        $this->widgetPoolProvider = $widgetPoolProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('content_embed_widgets', [$this->getWidgetEmbedder(), 'embed'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_widget_exists', [$this->getWidgetPool(), 'widgetExists']),
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
        return $this->getWidgetPool()->getWidget($name)->getContent();
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetEmbedderInterface
     */
    private function getWidgetEmbedder(): WidgetEmbedderInterface
    {
        return $this->widgetEmbedderProvider->getService();
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private function getWidgetPool(): WidgetPoolInterface
    {
        return $this->widgetPoolProvider->getService();
    }
}
