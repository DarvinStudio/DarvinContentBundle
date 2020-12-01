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

use Darvin\AdminBundle\CKEditor\AbstractCKEditorWidget;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Forward-to-controller widget
 */
class ForwardToControllerWidget extends AbstractCKEditorWidget
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
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string[]
     */
    private $sluggableEntityClasses;

    /**
     * @var array
     */
    private $options;

    /**
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
     * {@inheritDoc}
     */
    public function getContent(): ?string
    {
        $request = $this->requestStack->getCurrentRequest()->duplicate(null, null, [
            '_controller' => $this->controller,
        ]);

        return $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST)->getContent();
    }

    /**
     * {@inheritDoc}
     */
    public function getSluggableEntityClasses(): iterable
    {
        return $this->sluggableEntityClasses;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), $this->options);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
