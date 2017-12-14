<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Widget\Embedder;

use Darvin\ContentBundle\Widget\Embedder\Exception\RedirectException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Widget embedder redirect event subscriber
 */
class RedirectSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'redirect',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event Event
     */
    public function redirect(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof \Twig_Error) {
            return;
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof RedirectException) {
            $event->setResponse(new RedirectResponse($previous->getUrl(), $previous->getStatus(), $previous->getHeaders()));
        }
    }
}
