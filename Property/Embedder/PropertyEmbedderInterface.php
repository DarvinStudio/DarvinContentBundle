<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Property\Embedder;

/**
 * Property embedder
 */
interface PropertyEmbedderInterface
{
    /**
     * @param string|null $content Content
     * @param object|null $object  Object
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function embedProperties(?string $content, ?object $object = null): string;
}
