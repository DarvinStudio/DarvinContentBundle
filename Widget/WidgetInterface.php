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
 * Widget
 */
interface WidgetInterface
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string[]
     */
    public function getSluggableEntityClasses();

    /**
     * @param object $entity Entity
     *
     * @return bool
     */
    public function isEntitySluggable($entity);

    /**
     * @return array
     */
    public function getResolvedOptions();

    /**
     * @return string
     */
    public function getName();
}
