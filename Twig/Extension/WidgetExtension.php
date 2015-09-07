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

use Darvin\ContentBundle\Widget\WidgetException;
use Darvin\ContentBundle\Widget\WidgetInterface;

/**
 * Widget Twig extension
 */
class WidgetExtension extends \Twig_Extension
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    private $widgets;

    /**
     * @var string
     */
    private $widgetContents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->widgets = array();
        $this->widgetContents = array();
    }

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget Widget
     *
     * @throws \Darvin\ContentBundle\Widget\WidgetException
     */
    public function addWidget(WidgetInterface $widget)
    {
        $placeholder = $widget->getPlaceholder();

        if (isset($this->widgets[$placeholder])) {
            throw new WidgetException(sprintf('Widget with placeholder "%s" already added.', $placeholder));
        }

        $this->widgets[$placeholder] = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('darvin_content_embed_widgets', array($this, 'embedWidgets'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $content Content
     *
     * @return string
     */
    public function embedWidgets($content)
    {
        if (empty($content)) {
            return $content;
        }
        foreach ($this->widgets as $placeholder => $widget) {
            if (false === strpos($content, $placeholder)) {
                continue;
            }

            $widgetContent = isset($this->widgetContents[$placeholder])
                ? $this->widgetContents[$placeholder]
                : $widget->getContent();
            $this->widgetContents[$placeholder] = $widgetContent;

            $content = str_replace($placeholder, $widgetContent, $content);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_content_widget_extension';
    }
}
