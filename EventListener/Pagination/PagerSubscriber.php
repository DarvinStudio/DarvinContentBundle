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
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Event\AfterEvent;
use Knp\Component\Pager\Event\PaginationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Pager event subscriber
 */
class PagerSubscriber implements EventSubscriberInterface
{
    public const REQUEST_ATTR_PAGE_NUMBER = '_darvin_content_page_number';
    public const REQUEST_ATTR_PAGE_PARAMS = '_darvin_content_page_params';
    public const REQUEST_ATTR_PAGE_PREV   = '_darvin_content_page_prev';
    public const REQUEST_ATTR_PAGE_NEXT   = '_darvin_content_page_next';

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
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.pagination' => ['setPageAttributesToRequest', 2],
            'knp_pager.after'      => ['after', 2],
        ];
    }

    /**
     * @param \Knp\Component\Pager\Event\PaginationEvent $event Event
     */
    public function setPageAttributesToRequest(PaginationEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $pageParam = $event->options['pageParameterName'];

        // Set page number attribute to -1 in case there is more than one pagination
        $pageNumberAttr = $request->attributes->has(self::REQUEST_ATTR_PAGE_NUMBER) ? -1 : $request->query->get($pageParam, 1);

        $pageParamsAttr   = $request->attributes->get(self::REQUEST_ATTR_PAGE_PARAMS, []);
        $pageParamsAttr[] = $pageParam;

        $request->attributes->set(self::REQUEST_ATTR_PAGE_NUMBER, $pageNumberAttr);
        $request->attributes->set(self::REQUEST_ATTR_PAGE_PARAMS, $pageParamsAttr);
    }

    /**
     * @param \Knp\Component\Pager\Event\AfterEvent $event Event
     *
     * @throws \Darvin\ContentBundle\Pagination\PageNotFoundException
     */
    public function after(AfterEvent $event): void
    {
        $pagination = $event->getPaginationView();

        if (!$pagination instanceof SlidingPagination) {
            return;
        }

        $page = $pagination->getPage();

        if (null === $page) {
            $page = 1;
        } else {
            $pageNumber = (int)$page;

            if ((string)$pageNumber !== (string)$page
                || $pageNumber < 0
                || (!$pagination->getPaginatorOption('allowPageNumberExceed') && $pageNumber > $pagination->getPageCount())
            ) {
                throw new PageNotFoundException();
            }
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            if ($page > 1) {
                $request->attributes->set(self::REQUEST_ATTR_PAGE_PREV, $page - 1);
            }
            if ($page < $pagination->getPageCount()) {
                $request->attributes->set(self::REQUEST_ATTR_PAGE_NEXT, $page + 1);
            }
        }
    }
}
