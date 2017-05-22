<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Pagination;

use Darvin\ContentBundle\Pagination\PageNotFoundException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Page not found exception event listener
 */
class PageNotFoundExceptionListener
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event Event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $pageNotFoundException = $this->getPageNotFoundException($event);

        if (!empty($pageNotFoundException)) {
            $event->setException(new NotFoundHttpException($pageNotFoundException->getMessage()));
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event Event
     *
     * @return \Darvin\ContentBundle\Pagination\PageNotFoundException|null
     */
    private function getPageNotFoundException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof PageNotFoundException) {
            return $exception;
        }
        if ($exception->getPrevious() instanceof PageNotFoundException) {
            return $exception->getPrevious();
        }

        return null;
    }
}
