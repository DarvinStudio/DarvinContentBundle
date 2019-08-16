<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

/**
 * Sorting attribute renderer
 */
class AttributeRenderer implements AttributeRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function renderContainerAttr(array $attr = []): string
    {
        $attr['class'] = trim(sprintf('%s js-content-sortable', $attr['class'] ?? ''));

        return $this->renderAttr($attr);
    }

    /**
     * {@inheritDoc}
     */
    public function renderItemAttr(array $attr = []): string
    {
        return $this->renderAttr($attr);
    }

    /**
     * @param array $attr Attributes
     *
     * @return string
     */
    private function renderAttr(array $attr): string
    {
        $parts = [];

        foreach ($attr as $name => $value) {
            $parts[] = sprintf('%s="%s"', $name, $value);
        }
        if (empty($parts)) {
            return '';
        }

        return sprintf(' %s', implode(' ', $parts));
    }
}
