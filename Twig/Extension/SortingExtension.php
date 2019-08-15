<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\ContentBundle\Sorting\SorterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Sorting Twig extension
 */
class SortingExtension extends AbstractExtension
{
    /**
     * @var \Darvin\ContentBundle\Sorting\SorterInterface
     */
    private $sorter;

    /**
     * @param \Darvin\ContentBundle\Sorting\SorterInterface $sorter Sorter
     */
    public function __construct(SorterInterface $sorter)
    {
        $this->sorter = $sorter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('content_sort', [$this->sorter, 'sort']),
        ];
    }
}
