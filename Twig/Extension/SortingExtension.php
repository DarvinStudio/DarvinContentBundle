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

use Darvin\ContentBundle\Sorting\AttributeRendererInterface;
use Darvin\ContentBundle\Sorting\SorterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Sorting Twig extension
 */
class SortingExtension extends AbstractExtension
{
    /**
     * @var \Darvin\ContentBundle\Sorting\AttributeRendererInterface
     */
    private $attributeRenderer;

    /**
     * @var \Darvin\ContentBundle\Sorting\SorterInterface
     */
    private $sorter;

    /**
     * @param \Darvin\ContentBundle\Sorting\AttributeRendererInterface $attributeRenderer Sorting attribute renderer
     * @param \Darvin\ContentBundle\Sorting\SorterInterface            $sorter            Sorter
     */
    public function __construct(AttributeRendererInterface $attributeRenderer, SorterInterface $sorter)
    {
        $this->attributeRenderer = $attributeRenderer;
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

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_sort_container_attr', [$this->attributeRenderer, 'renderContainerAttr'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('content_sort_item_attr', [$this->attributeRenderer, 'renderItemAttr'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
