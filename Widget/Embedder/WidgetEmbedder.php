<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget\Embedder;

use Darvin\ContentBundle\EventListener\Pagination\PagerSubscriber;
use Darvin\ContentBundle\Widget\Embedder\Exception\HttpException;
use Darvin\ContentBundle\Widget\WidgetInterface;
use Darvin\ContentBundle\Widget\WidgetRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException as KernelHttpException;

/**
 * Widget embedder
 */
class WidgetEmbedder implements WidgetEmbedderInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetRegistryInterface
     */
    private $widgetRegistry;

    /**
     * @var array
     */
    private $widgetContents;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack       $requestStack   Request stack
     * @param \Darvin\ContentBundle\Widget\WidgetRegistryInterface $widgetRegistry Widget registry
     */
    public function __construct(RequestStack $requestStack, WidgetRegistryInterface $widgetRegistry)
    {
        $this->requestStack = $requestStack;
        $this->widgetRegistry = $widgetRegistry;

        $this->widgetContents = [];
    }

    /**
     * {@inheritDoc}
     */
    public function embed(?string $content, bool $onlyWidgetsOnNonFirstPage = false): ?string
    {
        $content = (string)$content;

        if ('' === $content) {
            return null;
        }

        $replacements = [];

        foreach ($this->widgetRegistry->getAllWidgets() as $widget) {
            $placeholder = '%'.$widget->getName().'%';

            if (false !== strpos($content, $placeholder)) {
                $replacements[$placeholder] = $this->getWidgetContent($widget);
            }
        }
        if (!$onlyWidgetsOnNonFirstPage) {
            return strtr($content, $replacements);
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            foreach ($request->attributes->get(PagerSubscriber::REQUEST_ATTR_PAGE_PARAMS, []) as $param) {
                if (1 !== (int)$request->query->get($param)) {
                    return implode($replacements);
                }
            }
        }

        return strtr($content, $replacements);
    }

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetInterface $widget Widget
     *
     * @return string|null
     * @throws \Darvin\ContentBundle\Widget\Embedder\Exception\HttpException
     */
    private function getWidgetContent(WidgetInterface $widget): ?string
    {
        if (!array_key_exists($widget->getName(), $this->widgetContents)) {
            try {
                $this->widgetContents[$widget->getName()] = $widget->getContent();
            } catch (KernelHttpException $ex) {
                throw new HttpException($ex);
            }
        }

        return $this->widgetContents[$widget->getName()];
    }
}
