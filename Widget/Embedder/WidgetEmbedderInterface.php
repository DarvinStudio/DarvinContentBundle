<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget\Embedder;

/**
 * Widget embedder
 */
interface WidgetEmbedderInterface
{
    /**
     * @param string|null $content                   Content
     * @param bool        $onlyWidgetsOnNonFirstPage Whether to render only widgets on non-first page
     *
     * @return string|null
     * @throws \Darvin\ContentBundle\Widget\Embedder\Exception\HttpException
     */
    public function embed(?string $content, bool $onlyWidgetsOnNonFirstPage = false): ?string;
}
