<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

/**
 * Content controller pool
 */
class ContentControllerPool
{
    /**
     * @var \Darvin\ContentBundle\Controller\ContentControllerInterface[]
     */
    private $controllers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controllers = [];
    }

    /**
     * @param \Darvin\ContentBundle\Controller\ContentControllerInterface $controller Content controller
     *
     * @throws \Darvin\ContentBundle\Controller\ControllerException
     */
    public function addController(ContentControllerInterface $controller)
    {
        $contentClass = $controller->getContentClass();

        if (isset($this->controllers[$contentClass])) {
            throw new ControllerException(sprintf('Content controller for class "%s" already added.', $contentClass));
        }

        $this->controllers[$contentClass] = $controller;
    }

    /**
     * @param string $contentClass Content class
     *
     * @return \Darvin\ContentBundle\Controller\ContentControllerInterface
     * @throws \Darvin\ContentBundle\Controller\ControllerException
     */
    public function getController($contentClass)
    {
        if (isset($this->controllers[$contentClass])) {
            return $this->controllers[$contentClass];
        }
        foreach (class_parents($contentClass) as $parent) {
            if (isset($this->controllers[$parent])) {
                return $this->controllers[$parent];
            }
        }

        throw new ControllerException(sprintf('Content controller for class "%s" does not exist.', $contentClass));
    }
}
