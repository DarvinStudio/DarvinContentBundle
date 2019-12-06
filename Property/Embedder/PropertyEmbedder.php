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

use Darvin\Utils\Strings\StringsUtil;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property embedder
 */
class PropertyEmbedder implements PropertyEmbedderInterface
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritDoc}
     */
    public function embedProperties(?string $content, ?object $object = null): string
    {
        if (null === $content || '' === $content) {
            return '';
        }

        preg_match_all('/%(\w+(\.\w+)*)%/', $content, $matches);

        $properties = $matches[1];

        if (empty($properties)) {
            return $content;
        }

        $properties   = array_unique($properties);
        $replacements = [];

        if (null !== $object) {
            foreach ($properties as $property) {
                $replacements[sprintf('%%%s%%', $property)] = $this->propertyAccessor->getValue($object, StringsUtil::toCamelCase($property));
            }
        }
        if (!empty($replacements)) {
            $content = strtr($content, $replacements);
        }

        return $content;
    }
}