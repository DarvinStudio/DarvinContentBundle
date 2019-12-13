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
class MetaTagProvider implements MetaTagProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getHeading(object $object, ?string $heading, ?string $fallback = null, ?callable $templateCallback = null): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaTitle(object $object, ?string $metaTitle, ?string $fallback = null, ?callable $templateCallback = null): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaDescription(object $object, ?string $metaDescription, ?string $fallback = null, ?callable $templateCallback = null): string
    {
        return '';
    }
}
