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

    /** @var string[] */
    private $sluggableEntityClasses;

    /** @var  array */
    private $options;

    /**
     * ForwardToControllerWidget constructor.
     * @param HttpKernelInterface $httpKernel
     * @param RequestStack $requestStack
     * @param string $name
     * @param string $controller
     * @param string[] $sluggableEntityClasses
     * @param array $options
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        RequestStack $requestStack,
        $name,
        $controller,
        array $sluggableEntityClasses,
        array $options
    ) {
        $this->httpKernel = $httpKernel;
        $this->requestStack = $requestStack;
        $this->name = $name;
        $this->controller = $controller;
        $this->sluggableEntityClasses = $sluggableEntityClasses;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $request = $this->requestStack->getCurrentRequest()->duplicate(null, null, [
            '_controller' => $this->controller,
        ]);

        return $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST)->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getSluggableEntityClasses()
    {
        return array_merge(parent::getSluggableEntityClasses(), $this->sluggableEntityClasses);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
