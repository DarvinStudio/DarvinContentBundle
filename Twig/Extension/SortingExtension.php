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
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_sort_attr', [$this, 'renderAttributes'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig\Environment $twig Twig
     * @param array             $attr Attributes
     *
     * @return string
     */
    public function renderAttributes(Environment $twig, array $attr = []): string
    {
        $attr['class'] = trim(sprintf('%s js-content-sortable', $attr['class'] ?? ''));

        return $twig->render('@DarvinContent/sorting/attributes.html.twig', [
            'attr' => $attr,
        ]);
    }
}
