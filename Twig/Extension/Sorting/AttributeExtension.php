<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension\Sorting;

use Darvin\ContentBundle\Sorting\AttributeRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Sorting attribute Twig extension
 */
class AttributeExtension extends AbstractExtension
{
    /**
     * @var \Darvin\ContentBundle\Sorting\AttributeRendererInterface
     */
    private $attributeRenderer;

    /**
     * @param \Darvin\ContentBundle\Sorting\AttributeRendererInterface $attributeRenderer Sorting attribute renderer
     */
    public function __construct(AttributeRendererInterface $attributeRenderer)
    {
        $this->attributeRenderer = $attributeRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_sort_attr', [$this->attributeRenderer, 'renderContainerAttr'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('content_sort_item_attr', [$this->attributeRenderer, 'renderItemAttr'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
