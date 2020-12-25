<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

/**
 * Widget registry
 */
class WidgetRegistry implements WidgetRegistryInterface
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
     * @var bool
     */
    private $initialized;

    /**
     * @param string[] $widgetBlacklist Blacklist of widget names or services IDs
     */
    public function __construct(array $widgetBlacklist)
    {
        $this->widgetBlacklist = $widgetBlacklist;

        $this->widgets = $this->widgetFactories = [];
        $this->initialized = false;
    }

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget Widget
     *
     * @throws \InvalidArgumentException
     */
    public function addWidget(WidgetInterface $widget): void
    {
        $name = $widget->getName();

        if (in_array($name, $this->widgetBlacklist)) {
            return;
        }
        if (isset($this->widgets[$name])) {
            throw new \InvalidArgumentException(sprintf('Widget "%s" already added to registry.', $name));
        }

        $this->widgets[$name] = $widget;
    }

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetFactoryInterface $widgetFactory Widget factory
     *
     * @throws \InvalidArgumentException
     */
    public function addWidgetFactory(WidgetFactoryInterface $widgetFactory): void
    {
        $class = get_class($widgetFactory);

        if (isset($this->widgetFactories[$class])) {
            throw new \InvalidArgumentException(sprintf('Widget factory "%s" already added to registry.', $class));
        }

        $this->widgetFactories[$class] = $widgetFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getWidget(string $name): WidgetInterface
    {
        $this->init();

        if (!$this->widgetExists($name)) {
            throw new \InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
        }

        return $this->widgets[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function widgetExists(string $name): bool
    {
        $this->init();

        return isset($this->widgets[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllWidgets(): iterable
    {
        $this->init();

        return $this->widgets;
    }

    private function init(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        foreach ($this->widgetFactories as $widgetFactory) {
            foreach ($widgetFactory->createWidgets() as $widget) {
                $this->addWidget($widget);
            }
        }
    }
}
