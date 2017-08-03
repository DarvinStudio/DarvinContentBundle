<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\Utils\Service\ServiceProviderInterface;

/**
 * Widget Twig extension
 */
class WidgetExtension extends \Twig_Extension
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
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('content_embed_widgets', [$this->getWidgetEmbedder(), 'embed'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('content_widget_exists', [$this->getWidgetPool(), 'widgetExists']),
            new \Twig_SimpleFunction('content_widget_render', [$this, 'renderWidget'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param string $name Widget name
     *
     * @return string
     */
    public function renderWidget($name)
    {
        return $this->getWidgetPool()->getWidget($name)->getContent();
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetEmbedderInterface
     */
    private function getWidgetEmbedder()
    {
        return $this->widgetEmbedderProvider->getService();
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private function getWidgetPool()
    {
        return $this->widgetPoolProvider->getService();
    }
}
