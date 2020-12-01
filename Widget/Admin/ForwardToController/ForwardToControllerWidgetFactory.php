<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget\Admin\ForwardToController;

use Darvin\ContentBundle\Widget\WidgetFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Forward-to-controller widget factory
 */
class ForwardToControllerWidgetFactory implements WidgetFactoryInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * Array with widget name in keys; controller reference, ckeditor_plugin_path and widget options in values
     *
     * @var array
     */
    private $controllers;

    /**
     * @var array
     */
    private $requiredParams = ['controller'];

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel     HTTP kernel
     * @param \Symfony\Component\HttpFoundation\RequestStack    $requestStack   Request stack
     * @param array                                             $controllers    Controllers
     * @param array                                             $requiredParams Required parameters
     */
    public function __construct(HttpKernelInterface $httpKernel, RequestStack $requestStack, array $controllers, array $requiredParams = [])
    {
        $this->httpKernel = $httpKernel;
        $this->requestStack = $requestStack;
        $this->controllers = $controllers;
        $this->requiredParams = array_merge($requiredParams, $this->requiredParams);
    }

    /**
     * {@inheritDoc}
     */
    public function createWidgets(): iterable
    {
        foreach ($this->controllers as $name => $setting) {
            $this->validateWidgetSetting($setting);

            yield new ForwardToControllerWidget(
                $this->httpKernel,
                $this->requestStack,
                $name,
                $setting['controller'],
                $setting['sluggable_entity_classes'] ?? [],
                $setting['options'] ?? []
            );
        }
    }

    /**
     * @param array $setting Setting
     *
     * @throws \InvalidArgumentException
     */
    private function validateWidgetSetting(array $setting): void
    {
        foreach ($this->requiredParams as $param) {
            if (!isset($setting[$param])) {
                throw new \InvalidArgumentException(sprintf('"%s" param must be set for "ForwardToController" widget.', $param));
            }
        }
    }
}
