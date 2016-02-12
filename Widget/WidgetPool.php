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
 * Widget pool
 */
class WidgetPool implements WidgetPoolInterface
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    private $widgets;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetFactoryInterface[]
     */
    private $widgetFactories;

    /**
     * @var array
     */
    private $placeholderCounts;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->widgets = $this->widgetFactories = $this->placeholderCounts = array();
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addWidget(WidgetInterface $widget, $duplicatePlaceholderException = true)
    {
        $placeholder = $widget->getPlaceholder();

        if (isset($this->widgets[$placeholder]) && $duplicatePlaceholderException) {
            throw new WidgetException(sprintf('Widget with placeholder "%s" already added to pool.', $placeholder));
        }

        $this->widgets[$placeholder] = $widget;

        if (!isset($this->placeholderCounts[$placeholder])) {
            $this->placeholderCounts[$placeholder] = 0;
        }

        $this->placeholderCounts[$placeholder]++;
    }

    /**
     * {@inheritdoc}
     */
    public function addWidgetFactory(WidgetFactoryInterface $widgetFactory)
    {
        $class = get_class($widgetFactory);

        if (isset($this->widgetFactories[$class])) {
            throw new WidgetException(sprintf('Widget factory "%s" already added to pool.', $class));
        }

        $this->widgetFactories[$class] = $widgetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWidgets()
    {
        $this->init();

        return $this->widgets;
    }

    /**
     * {@inheritdoc}
     */
    public function isWidgetUnique($placeholder)
    {
        $this->init();

        return !isset($this->placeholderCounts[$placeholder]) || 1 === $this->placeholderCounts[$placeholder];
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        foreach ($this->widgetFactories as $widgetFactory) {
            foreach ($widgetFactory->createWidgets() as $widget) {
                $this->addWidget($widget, false);
            }
        }
    }
}
