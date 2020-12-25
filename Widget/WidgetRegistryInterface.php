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

class_alias(WidgetRegistryInterface::class, 'Darvin\ContentBundle\Widget\WidgetPoolInterface');

/**
 * Widget registry
 */
interface WidgetRegistryInterface
{
    /**
     * @param string $name Widget name
     *
     * @return \Darvin\ContentBundle\Widget\WidgetInterface
     * @throws \InvalidArgumentException
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
