<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Switch locale event subscriber
 */
class SwitchLocaleSubscriber implements EventSubscriberInterface
{
    const SESSION_KEY = 'app_switch_locale';

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var bool
     */
    private $localeIsSwitching;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router Router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;

        $this->localeIsSwitching = false;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => 'detectLocaleSwitching',
            KernelEvents::EXCEPTION => 'redirectToHomepage',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event Event
     */
    public function detectLocaleSwitching(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() || !$event->getRequest()->getSession()->has(self::SESSION_KEY)) {
            return;
        }

        $event->getRequest()->getSession()->remove(self::SESSION_KEY);

        $this->localeIsSwitching = true;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event Event
     */
    public function redirectToHomepage(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->localeIsSwitching) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(new RedirectResponse($this->router->generate('darvin_page_homepage')));
        }
    }
}
