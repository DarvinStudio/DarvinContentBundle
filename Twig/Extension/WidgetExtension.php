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
     * @param \Darvin\ContentBundle\Widget\WidgetEmbedderInterface $widgetEmbedder Widget embedder
     */
    public function __construct(WidgetEmbedderInterface $widgetEmbedder)
    {
        $this->widgetEmbedder = $widgetEmbedder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('content_embed_widgets', [$this->widgetEmbedder, 'embed'], ['is_safe' => ['html']]),
        ];
    }
}
