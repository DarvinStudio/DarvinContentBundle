<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Pagination;

use Knp\Component\Pager\Event;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator as BasePaginator;

/**
 * Paginator
 */
class Paginator extends BasePaginator
{
    /**
     * {@inheritDoc}
     */
    public function paginate($target, $page = 1, $limit = 10, array $options = []): PaginationInterface
    {
        $page = (int) $page;
        $limit = intval(abs($limit));
        if (!$limit) {
            throw new \LogicException("Invalid item per page number, must be a positive number");
        }
        $offset = 0 === $page ? 0 : (abs($page - 1) * $limit);
        $options = array_merge($this->defaultOptions, $options);

        // normalize default sort field
        if (isset($options[self::DEFAULT_SORT_FIELD_NAME]) && is_array($options[self::DEFAULT_SORT_FIELD_NAME])) {
            $options[self::DEFAULT_SORT_FIELD_NAME] = implode('+', $options[self::DEFAULT_SORT_FIELD_NAME]);
        }

        // default sort field and direction are set based on options (if available)
        if (!isset($_GET[$options[self::SORT_FIELD_PARAMETER_NAME]]) && isset($options[self::DEFAULT_SORT_FIELD_NAME])) {
            $_GET[$options[self::SORT_FIELD_PARAMETER_NAME]] = $options[self::DEFAULT_SORT_FIELD_NAME];

            if (!isset($_GET[$options[self::SORT_DIRECTION_PARAMETER_NAME]])) {
                $_GET[$options[self::SORT_DIRECTION_PARAMETER_NAME]] = isset($options[self::DEFAULT_SORT_DIRECTION]) ? $options[self::DEFAULT_SORT_DIRECTION] : 'asc';
            }
        }

        // before pagination start
        $beforeEvent = new Event\BeforeEvent($this->eventDispatcher);
        $this->eventDispatcher->dispatch('knp_pager.before', $beforeEvent);
        // items
        $itemsEvent = new Event\ItemsEvent($offset, 0 === $page ? PHP_INT_MAX : $limit);
        $itemsEvent->options = &$options;
        $itemsEvent->target = &$target;
        $this->eventDispatcher->dispatch('knp_pager.items', $itemsEvent);
        if (!$itemsEvent->isPropagationStopped()) {
            throw new \RuntimeException('One of listeners must count and slice given target');
        }
        // pagination initialization event
        $paginationEvent = new Event\PaginationEvent;
        $paginationEvent->target = &$target;
        $paginationEvent->options = &$options;
        $this->eventDispatcher->dispatch('knp_pager.pagination', $paginationEvent);
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
        $this->eventDispatcher->dispatch('knp_pager.after', $afterEvent);
        return $paginationView;
    }
}
