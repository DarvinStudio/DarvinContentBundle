<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Knp\Component\Pager\Event\PaginationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Set page to request event subscriber
 */
class SetPageToRequestSubscriber implements EventSubscriberInterface
{
    const ATTR_NAME = '_darvin_content_page';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'knp_pager.pagination' => ['onPagination', 2],
        ];
    }

    /**
     * @param \Knp\Component\Pager\Event\PaginationEvent $event Event
     */
    public function onPagination(PaginationEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (empty($request)) {
            return;
        }

        // Set page to -1 in case there is more than one pagination
        $page = $request->attributes->has(self::ATTR_NAME) ? -1 : $request->query->get($event->options['pageParameterName'], 1);

        $request->attributes->set(self::ATTR_NAME, $page);
    }
}
