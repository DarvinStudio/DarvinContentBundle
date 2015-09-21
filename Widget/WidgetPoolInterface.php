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
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget Widget
     */
    public function add(WidgetInterface $widget);

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    public function getAll();
}