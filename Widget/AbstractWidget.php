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
 * Widget abstract implementation
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSluggableEntityClasses()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isEntitySluggable($entity)
    {
        foreach ($this->getSluggableEntityClasses() as $sluggable) {
            if ($entity instanceof $sluggable) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return '%'.$this->getName().'%';
    }
}
