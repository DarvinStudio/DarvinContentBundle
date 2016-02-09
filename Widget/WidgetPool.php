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

use Darvin\ContentBundle\Event\Events;
use Darvin\ContentBundle\Event\WidgetPoolEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Widget pool
 */
class WidgetPool implements WidgetPoolInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    private $widgets;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher Event dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->widgets = array();
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
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
    public function getAllWidgets()
    {
        $this->init();

        return $this->widgets;
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        $this->eventDispatcher->dispatch(Events::WIDGET_POOL_INIT, new WidgetPoolEvent($this));
    }
}
