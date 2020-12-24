<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

/**
 * Content reference rebuilder
 */
interface ContentReferenceRebuilderInterface
{
    /**
     * @param callable|null $output Output callback
     */
    public function rebuildContentReferences(?callable $output = null): void;
}
