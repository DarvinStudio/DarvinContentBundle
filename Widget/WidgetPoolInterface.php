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
interface WidgetPoolInterface
{
    /**
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget                        Widget
     * @param bool                                         $duplicatePlaceholderException Whether to throw exception on duplicate widget placeholder
     */
    public function addWidget(WidgetInterface $widget, $duplicatePlaceholderException = true);

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetFactoryInterface $widgetFactory Widget factory
     */
    public function addWidgetFactory(WidgetFactoryInterface $widgetFactory);

    /**
     * @param string $placeholder Widget placeholder
     *
     * @return \Darvin\ContentBundle\Widget\WidgetInterface
     * @throws \Darvin\ContentBundle\Widget\WidgetException
     */
    public function getWidget($placeholder);

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    public function getAllWidgets();

    /**
     * @param string $placeholder Widget placeholder
     *
     * @return bool
     */
    public function isWidgetUnique($placeholder);
}
