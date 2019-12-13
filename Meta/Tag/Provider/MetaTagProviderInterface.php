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
     * @param string|null   $heading          Original heading
     * @param string|null   $fallback         Fallback
     * @param callable|null $templateCallback Template callback
     *
     * @return string
     */
    public function getHeading(object $object, ?string $heading, ?string $fallback = null, ?callable $templateCallback = null): string;

    /**
     * @param object        $object           Object
     * @param string|null   $metaTitle        Original meta title
     * @param string|null   $fallback         Fallback
     * @param callable|null $templateCallback Template callback
     *
     * @return string
     */
    public function getMetaTitle(object $object, ?string $metaTitle, ?string $fallback = null, ?callable $templateCallback = null): string;

    /**
     * @param object        $object           Object
     * @param string|null   $metaDescription  Original meta description
     * @param string|null   $fallback         Fallback
     * @param callable|null $templateCallback Template callback
     *
     * @return string
     */
    public function getMetaDescription(object $object, ?string $metaDescription, ?string $fallback = null, ?callable $templateCallback = null): string;
}
