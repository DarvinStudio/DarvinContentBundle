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
     * @var string[]
     */
    private $widgetBlacklist;

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
    private $nameCounts;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param string[] $widgetBlacklist Blacklist of widget names or services IDs
     */
    public function __construct(array $widgetBlacklist)
    {
        $this->widgetBlacklist = $widgetBlacklist;

        $this->widgets = $this->widgetFactories = $this->nameCounts = [];
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addWidget(WidgetInterface $widget, $duplicateNameException = true)
    {
        $name = $widget->getName();

        if (in_array($name, $this->widgetBlacklist)) {
            return;
        }
        if (isset($this->widgets[$name]) && $duplicateNameException) {
            throw new WidgetException(sprintf('Widget "%s" already added to pool.', $name));
        }

        $this->widgets[$name] = $widget;

        if (!isset($this->nameCounts[$name])) {
            $this->nameCounts[$name] = 0;
        }

        $this->nameCounts[$name]++;
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
    public function getWidget($name)
    {
        $this->init();

        if (!$this->widgetExists($name)) {
            throw new WidgetException(sprintf('Widget "%s" does not exist.', $name));
        }

        return $this->widgets[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function widgetExists($name)
    {
        $this->init();

        return isset($this->widgets[$name]);
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
    public function isWidgetUnique($name)
    {
        $this->init();

        return !isset($this->nameCounts[$name]) || 1 === $this->nameCounts[$name];
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
