<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

/**
 * Widget embedder
 */
interface WidgetEmbedderInterface
{
    /**
     * @param string $content                   Content
     * @param bool   $onlyWidgetsOnNonFirstPage Whether to render only widgets on non-first page
     *
     * @return string
     * @throws \Darvin\ContentBundle\Widget\Embedder\Exception\HttpException
     */
    public function embed($content, $onlyWidgetsOnNonFirstPage = false);
}
