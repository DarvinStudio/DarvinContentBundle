<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Event;

use Darvin\ContentBundle\Widget\WidgetPoolInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Widget pool event
 */
class WidgetPoolEvent extends Event
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface $widgetPool Widget pool
     */
    public function __construct(WidgetPoolInterface $widgetPool)
    {
        $this->widgetPool = $widgetPool;
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    public function getWidgetPool()
    {
        return $this->widgetPool;
    }
}
