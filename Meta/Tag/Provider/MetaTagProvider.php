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

use Darvin\ContentBundle\Property\Embedder\PropertyEmbedderInterface;

/**
 * Meta tag provider
 */
class MetaTagProvider implements MetaTagProviderInterface
{
    /**
     * @var \Darvin\ContentBundle\Property\Embedder\PropertyEmbedderInterface
     */
    private $propertyEmbedder;

    /**
     * @param \Darvin\ContentBundle\Property\Embedder\PropertyEmbedderInterface $propertyEmbedder Property embedder
     */
    public function __construct(PropertyEmbedderInterface $propertyEmbedder)
    {
        $this->propertyEmbedder = $propertyEmbedder;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaTag(object $object, ?string $originalTag, ?string $fallback = null, ?callable $templateCallback = null): string
    {
        $template = null !== $originalTag && '' !== $originalTag ? $originalTag : (string)$templateCallback($object);

        $tag = $this->propertyEmbedder->embedProperties($template, $object);

        if ('' !== $tag) {
            return $tag;
        }
        if (null !== $fallback) {
            return $fallback;
        }

        return '';
    }
}
