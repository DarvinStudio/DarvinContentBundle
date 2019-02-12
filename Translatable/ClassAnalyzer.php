<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer as BaseClassAnalyzer;

/**
 * Translatable class analyzer
 */
class ClassAnalyzer extends BaseClassAnalyzer
{
    /**
     * {@inheritDoc}
     */
    public function hasTrait(\ReflectionClass $class, $traitName, $isRecursive = false): bool
    {
        return parent::hasTrait($class, $traitName, true);
    }
}
