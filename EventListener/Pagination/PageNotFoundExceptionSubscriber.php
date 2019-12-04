<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Pagination;

use Darvin\ContentBundle\Pagination\PageNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Page not found exception event subscriber
 */
class PageNotFoundExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'setHttpException',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event Event
     */
    public function setHttpException(ExceptionEvent $event): void
    {
        $pageNotFoundException = $this->getPageNotFoundException($event);

        if (null !== $pageNotFoundException) {
            $event->setThrowable(new NotFoundHttpException($pageNotFoundException->getMessage()));
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event Event
     *
     * @return \Darvin\ContentBundle\Pagination\PageNotFoundException|null
     */
    private function getPageNotFoundException(ExceptionEvent $event): ?PageNotFoundException
    {
        $exception = $event->getThrowable();

        if ($exception instanceof PageNotFoundException) {
            return $exception;
        }
        if ($exception->getPrevious() instanceof PageNotFoundException) {
            return $exception->getPrevious();
        }

        return null;
    }
}
