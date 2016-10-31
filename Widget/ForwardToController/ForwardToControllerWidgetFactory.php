<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 17.10.16
 * Time: 12:40
 */

namespace Darvin\ContentBundle\Widget\ForwardToController;

use Darvin\ContentBundle\Widget\WidgetException;
use Darvin\ContentBundle\Widget\WidgetFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Forward-to-controller widget factory
 */
class ForwardToControllerWidgetFactory implements WidgetFactoryInterface
{
    /** @var  HttpKernelInterface */
    private $httpKernel;

    /** @var  RequestStack */
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
     * ForwardToControllerWidgetFactory constructor.
     * @param HttpKernelInterface $httpKernel
     * @param RequestStack $requestStack
     * @param array $controllers
     * @param array $requiredParams
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        RequestStack $requestStack,
        array $controllers,
        $requiredParams = []
    )
    {
        $this->httpKernel = $httpKernel;
        $this->requestStack = $requestStack;
        $this->controllers = $controllers;

        if (count($requiredParams) > 0) {
            $this->requiredParams = array_merge($requiredParams, $this->requiredParams);
        }
    }

    /**
     * @return \Darvin\ContentBundle\Widget\WidgetInterface[]
     */
    public function createWidgets()
    {
        $widgets = [];
        foreach ($this->controllers as $name=>$setting) {
            $this->validateWidgetSetting($setting);

            $controller = $setting['controller'];
            $sluggableEntityClasses = isset($setting['sluggable_entity_classes']) ? $setting['sluggable_entity_classes'] : [];
            $options = isset($setting['options']) ? $setting['options'] : [];

            $widgets[] = new ForwardToControllerWidget(
                $this->httpKernel,
                $this->requestStack,
                $name,
                $controller,
                $sluggableEntityClasses,
                $options
            );
        }

        return $widgets;
    }

    /**
     * @param array $setting Setting
     *
     * @throws \Darvin\ContentBundle\Widget\WidgetException
     */
    private function validateWidgetSetting(array $setting)
    {
        foreach ($this->requiredParams as $param) {
            if (!isset($setting[$param])) {
                new WidgetException(sprintf('"%s" param must be set for "ForwardToController" widget', $param));
            }
        }
    }
}