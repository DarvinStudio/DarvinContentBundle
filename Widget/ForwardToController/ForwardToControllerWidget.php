<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 17.10.16
 * Time: 13:00
 */

namespace Darvin\ContentBundle\Widget\ForwardToController;

use Darvin\AdminBundle\CKEditor\AbstractCKEditorWidget;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Forward-to-controller widget
 */
class ForwardToControllerWidget extends AbstractCKEditorWidget
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
     *
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel             HTTP kernel
     * @param \Symfony\Component\HttpFoundation\RequestStack    $requestStack           Request stack
     * @param string                                            $name                   Widget name
     * @param string                                            $controller             Controller
     * @param string[]                                          $sluggableEntityClasses Sluggable entity classes
     * @param array                                             $options                Options
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        RequestStack $requestStack,
        string $name,
        string $controller,
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
     * {@inheritdoc}
     */
    public function getContent(): ?string
    {
        $request = $this->requestStack->getCurrentRequest()->duplicate(null, null, [
            '_controller' => $this->controller,
        ]);

        return $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST)->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getSluggableEntityClasses(): iterable
    {
        return $this->sluggableEntityClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
