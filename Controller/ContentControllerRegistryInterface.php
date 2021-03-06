<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

class_alias(ContentControllerRegistryInterface::class, 'Darvin\ContentBundle\Controller\ContentControllerPoolInterface');

/**
 * Content controller registry
 */
interface ContentControllerRegistryInterface
{
    /**
     * @param string $contentClass Content class
     *
     * @return \Darvin\ContentBundle\Controller\ContentControllerInterface
     * @throws \Darvin\ContentBundle\Controller\ControllerNotExistsException
     */
    public function getController(string $contentClass): ContentControllerInterface;
}
