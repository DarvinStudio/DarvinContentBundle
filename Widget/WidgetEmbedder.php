<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

/**
 * Widget embedder
 */
class WidgetEmbedder implements WidgetEmbedderInterface
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @var array
     */
    private $widgetContents;

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface $widgetPool Widget pool
     */
    public function __construct(WidgetPoolInterface $widgetPool)
    {
        $this->widgetPool = $widgetPool;
        $this->widgetContents = [];
    }

    /**
     * {@inheritdoc}
     */
    public function embed($content)
    {
        if (empty($content)) {
            return $content;
        }
        foreach ($this->widgetPool->getAllWidgets() as $widget) {
            $placeholder = $widget->getPlaceholder();

            if (false === strpos($content, $placeholder)) {
                continue;
            }

            $content = str_replace($placeholder, $this->getWidgetContent($widget), $content);
        }

        return $content;
    }

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget Widget
     *
     * @return string
     */
    private function getWidgetContent(WidgetInterface $widget)
    {
        $placeholder = $widget->getPlaceholder();

        return isset($this->widgetContents[$placeholder]) ? $this->widgetContents[$placeholder] : $widget->getContent();
    }
}
