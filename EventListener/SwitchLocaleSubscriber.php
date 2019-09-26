<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\Utils\Homepage\HomepageRouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Switch locale event subscriber
 */
class SwitchLocaleSubscriber implements EventSubscriberInterface
{
    public const SESSION_KEY = 'darvin.content.locale.switch';

    /**
     * @var \Darvin\Utils\Homepage\HomepageRouterInterface
     */
    private $homepageRouter;

    /**
     * @var bool
     */
    private $localeIsSwitching;

    /**
     * @param \Darvin\Utils\Homepage\HomepageRouterInterface $homepageRouter Homepage router
     */
    public function __construct(HomepageRouterInterface $homepageRouter)
    {
        $this->homepageRouter = $homepageRouter;

        $this->localeIsSwitching = false;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST   => 'detectLocaleSwitching',
            KernelEvents::EXCEPTION => 'redirectToHomepage',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event Event
     */
    public function detectLocaleSwitching(RequestEvent $event): void
    {
        if (!$event->isMasterRequest() || !$event->getRequest()->getSession()->has(self::SESSION_KEY)) {
            return;
        }

        $event->getRequest()->getSession()->remove(self::SESSION_KEY);

        $this->localeIsSwitching = true;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event Event
     */
    public function redirectToHomepage(ExceptionEvent $event): void
    {
        if (!$event->isMasterRequest() || !$this->localeIsSwitching) {
            return;
        }

        $exception = $event->getException();

        if (!$exception instanceof HttpExceptionInterface) {
            return;
        }

        $url = $this->homepageRouter->generate();

        if (null !== $url) {
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
