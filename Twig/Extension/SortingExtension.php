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

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Sorting Twig extension
 */
class SortingExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('content_sort', [$this, 'sort']),
        ];
    }

    /**
     * @param iterable $objects Objects
     *
     * @return array
     */
    public function sort(iterable $objects): array
    {
        $sorted = [];

        foreach ($objects as $key => $object) {
            $sorted[$key] = $object;
        }

        return $sorted;
    }
}
