<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

use Darvin\ContentBundle\EventListener\Pagination\PagerSubscriber;
use Darvin\ContentBundle\Widget\Embedder\Exception\HttpException;
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
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @var array
     */
    private $widgetContents;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack   $requestStack Request stack
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface $widgetPool   Widget pool
     */
    public function __construct(RequestStack $requestStack, WidgetPoolInterface $widgetPool)
    {
        $this->requestStack = $requestStack;
        $this->widgetPool = $widgetPool;

        $this->widgetContents = [];
    }

    /**
     * {@inheritdoc}
     */
    public function embed($content, $onlyWidgetsOnNonFirstPage = false)
    {
        if (empty($content)) {
            return $content;
        }

        $replacements = [];

        foreach ($this->widgetPool->getAllWidgets() as $widget) {
            $placeholder = '%'.$widget->getName().'%';

            if (false !== strpos($content, $placeholder)) {
                $replacements[$placeholder] = $this->getWidgetContent($widget);
            }
        }
        if (!$onlyWidgetsOnNonFirstPage) {
            return strtr($content, $replacements);
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!empty($request)) {
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
     * @return string
     * @throws \Darvin\ContentBundle\Widget\Embedder\Exception\HttpException
     */
    private function getWidgetContent(WidgetInterface $widget)
    {
        if (!isset($this->widgetContents[$widget->getName()])) {
            try {
                $this->widgetContents[$widget->getName()] = $widget->getContent();
            } catch (KernelHttpException $ex) {
                throw new HttpException($ex);
            }
        }

        return $this->widgetContents[$widget->getName()];
    }
}
