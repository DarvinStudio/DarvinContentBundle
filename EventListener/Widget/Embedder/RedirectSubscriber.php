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

use Darvin\ContentBundle\Widget\Embedder\Exception\RedirectException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Error\Error;

/**
 * Widget embedder redirect event subscriber
 */
class RedirectSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'redirect',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event Event
     */
    public function redirect(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof Error) {
            return;
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof RedirectException) {
            $event->setResponse(new RedirectResponse($previous->getUrl(), $previous->getStatus(), $previous->getHeaders()));
        }
    }
}
