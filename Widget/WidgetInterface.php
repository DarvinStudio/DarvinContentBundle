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
 * Widget
 */
interface WidgetInterface
{
    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @return iterable|string[]
     */
    public function getSluggableEntityClasses(): iterable;

    /**
     * @param object $entity Entity
     *
     * @return bool
     */
    public function isSluggable(object $entity): bool;

    /**
     * @return string
     */
    public function getName(): string;
}
