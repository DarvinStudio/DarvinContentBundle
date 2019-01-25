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
 * Widget pool
 */
interface WidgetPoolInterface
{
    /**
     * @param string $name Widget name
     *
     * @return \Darvin\ContentBundle\Widget\WidgetInterface
     * @throws \Darvin\ContentBundle\Widget\Exception\WidgetNotExistsException
     */
    public function getWidget(string $name): WidgetInterface;

    /**
     * @param string $name Widget name
     *
     * @return bool
     */
    public function widgetExists(string $name): bool;

    /**
     * @return iterable|\Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    public function getAllWidgets(): iterable;

    /**
     * @param string $name Widget name
     *
     * @return bool
     */
    public function isWidgetUnique(string $name): bool;
}
