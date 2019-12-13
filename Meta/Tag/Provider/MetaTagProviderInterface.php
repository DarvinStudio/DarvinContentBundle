<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Meta\Tag\Provider;

/**
 * Meta tag provider
 */
interface MetaTagProviderInterface
{
    /**
     * @param object        $object           Object
     * @param string|null   $originalTag      Original meta tag
     * @param string|null   $fallback         Fallback
     * @param callable|null $templateCallback Template callback
     *
     * @return string
     */
    public function getMetaTag(object $object, ?string $originalTag, ?string $fallback = null, ?callable $templateCallback = null): string;
}
