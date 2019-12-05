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

use Darvin\Utils\Strings\StringsUtil;

/**
 * Widget abstract implementation
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * @var string|null
     */
    private $name = null;

    /**
     * {@inheritDoc}
     */
    public function getSluggableEntityClasses(): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function isSluggable($entity): bool
    {
        foreach ($this->getSluggableEntityClasses() as $sluggable) {
            if ($entity instanceof $sluggable) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        if (null === $this->name) {
            $this->name = StringsUtil::toUnderscore(preg_replace('/^.*\\\|Widget$/', '', get_class($this)));
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return '%'.$this->getName().'%';
    }
}
