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

use Darvin\ContentBundle\Widget\WidgetEmbedderInterface;
use Darvin\ContentBundle\Widget\WidgetPoolInterface;

/**
 * Widget Twig extension
 */
class WidgetExtension extends \Twig_Extension
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetEmbedderInterface
     */
    private $widgetEmbedder;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetEmbedderInterface $widgetEmbedder Widget embedder
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface     $widgetPool     Widget pool
     */
    public function __construct(WidgetEmbedderInterface $widgetEmbedder, WidgetPoolInterface $widgetPool)
    {
        $this->widgetEmbedder = $widgetEmbedder;
        $this->widgetPool = $widgetPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('content_embed_widgets', [$this->widgetEmbedder, 'embed'], [
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
        return $this->widgetPool->getWidget($name)->getContent();
    }
}
