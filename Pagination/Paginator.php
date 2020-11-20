<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Pagination;

use Knp\Component\Pager\Event;
use Knp\Component\Pager\Exception\PageNumberOutOfRangeException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator as BasePaginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Paginator
 */
class Paginator extends BasePaginator
{
    /**
     * {@inheritDoc}
     */
    public function paginate($target, int $page = 1, ?int $limit = null, array $options = []): PaginationInterface
    {
        $limit = $limit ?? $this->defaultOptions[self::DEFAULT_LIMIT];
        if ($limit <= 0 or $page < 0) {
            throw new \LogicException("Invalid item per page number. Limit: $limit and Page: $page, must be positive integers");
        }

        $offset = 0 === $page ? 0 : (($page - 1) * $limit);
        $options = array_merge($this->defaultOptions, $options);

        // normalize default sort field
        if (isset($options[self::DEFAULT_SORT_FIELD_NAME]) && is_array($options[self::DEFAULT_SORT_FIELD_NAME])) {
            $options[self::DEFAULT_SORT_FIELD_NAME] = implode('+', $options[self::DEFAULT_SORT_FIELD_NAME]);
        }

        $request = null === $this->requestStack ? Request::createFromGlobals() : $this->requestStack->getCurrentRequest();

        // default sort field and direction are set based on options (if available)
        if (isset($options[self::DEFAULT_SORT_FIELD_NAME]) && !$request->query->has($options[self::SORT_FIELD_PARAMETER_NAME])) {
            $request->query->set($options[self::SORT_FIELD_PARAMETER_NAME], $options[self::DEFAULT_SORT_FIELD_NAME]);

            if (!$request->query->has($options[self::SORT_DIRECTION_PARAMETER_NAME])) {
                $request->query->set($options[self::SORT_DIRECTION_PARAMETER_NAME], $options[self::DEFAULT_SORT_DIRECTION] ?? 'asc');
            }
        }

        // before pagination start
        $beforeEvent = new Event\BeforeEvent($this->eventDispatcher, $request);
        $this->dispatch('knp_pager.before', $beforeEvent);
        // items
        $itemsEvent = new Event\ItemsEvent($offset, 0 === $page ? PHP_INT_MAX : $limit);
        $itemsEvent->options = &$options;
        $itemsEvent->target = &$target;

        try {
            $this->dispatch('knp_pager.items', $itemsEvent);
        } catch (\UnexpectedValueException $ex) {
            throw new NotFoundHttpException($ex->getMessage(), $ex);
        }
        if (!$itemsEvent->isPropagationStopped()) {
            throw new \RuntimeException('One of listeners must count and slice given target');
        }
        if ($page > ceil($itemsEvent->count / $limit)) {
            $pageOutOfRangeOption = $options[self::PAGE_OUT_OF_RANGE] ?? $this->defaultOptions[self::PAGE_OUT_OF_RANGE];
            if ($pageOutOfRangeOption === self::PAGE_OUT_OF_RANGE_FIX) {
                // replace page number out of range with max page
                return $this->paginate($target, ceil($itemsEvent->count / $limit), $limit, $options);
            }
            if ($pageOutOfRangeOption === self::PAGE_OUT_OF_RANGE_THROW_EXCEPTION) {
                throw new PageNumberOutOfRangeException("Page number: $page is out of range.");
            }
        }

        // pagination initialization event
        $paginationEvent = new Event\PaginationEvent;
        $paginationEvent->target = &$target;
        $paginationEvent->options = &$options;
        $this->dispatch('knp_pager.pagination', $paginationEvent);
        if (!$paginationEvent->isPropagationStopped()) {
            throw new \RuntimeException('One of listeners must create pagination view');
        }
        // pagination class can be different, with different rendering methods
        $paginationView = $paginationEvent->getPagination();
        $paginationView->setCustomParameters($itemsEvent->getCustomPaginationParameters());
        $paginationView->setCurrentPageNumber($page);
        $paginationView->setItemNumberPerPage($limit);
        $paginationView->setTotalItemCount($itemsEvent->count);
        $paginationView->setPaginatorOptions($options);
        $paginationView->setItems($itemsEvent->items);

        // after
        $afterEvent = new Event\AfterEvent($paginationView);
        $this->dispatch('knp_pager.after', $afterEvent);
        return $paginationView;
    }
}
