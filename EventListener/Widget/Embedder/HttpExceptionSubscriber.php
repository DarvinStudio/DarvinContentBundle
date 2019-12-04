<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Widget\Embedder;

use Darvin\ContentBundle\Widget\Embedder\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Error\Error;

/**
 * Widget embedder HTTP exception event subscriber
 */
class HttpExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'raiseKernelHttpException',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event Event
     */
    public function raiseKernelHttpException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof Error) {
            return;
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof HttpException) {
            $event->setThrowable($previous->getKernelHttpException());
        }
    }
}
