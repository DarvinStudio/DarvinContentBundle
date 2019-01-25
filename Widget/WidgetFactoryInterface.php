<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

/**
 * Widget factory
 */
interface WidgetFactoryInterface
{
    /**
     * @return iterable|\Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    public function createWidgets(): iterable;
}
