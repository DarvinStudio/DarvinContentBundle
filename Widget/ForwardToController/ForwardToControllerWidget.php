<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 17.10.16
 * Time: 13:00
 */

namespace Darvin\ContentBundle\Widget\ForwardToController;


use Darvin\ContentBundle\Widget\AbstractWidget;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ForwardToControllerWidget extends AbstractWidget
{
    /** @var  HttpKernelInterface */
    private $httpKernel;

    /** @var  RequestStack */
    private $requestStack;

    /** @var string */
    private $name;

    /** @var string */
    private $controller;

    /** @var  array */
    private $options;

    /**
     * ForwardToControllerWidget constructor.
     * @param HttpKernelInterface $httpKernel
     * @param RequestStack $requestStack
     * @param string $name
     * @param string $controller
     * @param array $options
     */
    public function __construct(HttpKernelInterface $httpKernel, RequestStack $requestStack, $name, $controller, array $options)
    {
        $this->httpKernel = $httpKernel;
        $this->requestStack = $requestStack;
        $this->name = $name;
        $this->controller = $controller;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $request = $this->requestStack->getCurrentRequest()->duplicate(null, null, [
            '_controller' => $this->controller
        ]);

        return $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST)->getContent();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $parent = parent::getOptions();
        return array_merge($this->options, $parent);
    }
}