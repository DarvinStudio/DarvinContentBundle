<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

/**
 * Content controller not exists exception
 */
class ControllerNotExistsException extends \Exception
{
    /**
     * @param string $contentClass Content class
     */
    public function __construct(string $contentClass)
    {
        parent::__construct(sprintf('Content controller for class "%s" does not exist.', $contentClass));
    }
}
