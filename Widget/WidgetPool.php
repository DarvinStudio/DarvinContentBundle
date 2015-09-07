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
     * Constructor
     */
    public function __construct()
    {
        $this->widgets = array();
    }

    /**
     * {@inheritdoc}
     */
    public function add(WidgetInterface $widget)
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
    public function getAll()
    {
        return $this->widgets;
    }
}
