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

use Darvin\ContentBundle\Entity\GlobalPropertyInterface;
use Darvin\ContentBundle\Repository\GlobalPropertyRepository;
use Darvin\ContentBundle\Translatable\TranslatableException;
use Darvin\Utils\Locale\LocaleProviderInterface;
use Darvin\Utils\Strings\Stringifier\StringifierInterface;
use Darvin\Utils\Strings\StringsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property embedder
 */
class PropertyEmbedder implements PropertyEmbedderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\Utils\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Darvin\Utils\Strings\Stringifier\StringifierInterface
     */
    private $stringifier;

    /**
     * @var array
     */
    private $callbacks;

    /**
     * @var array|null
     */
    private $globals;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                        $em               Entity manager
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                $localeProvider   Locale provider
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     * @param \Darvin\Utils\Strings\Stringifier\StringifierInterface      $stringifier      Stringifier
     * @param array                                                       $callbacks        Callbacks
     */
    public function __construct(
        EntityManagerInterface $em,
        LocaleProviderInterface $localeProvider,
        PropertyAccessorInterface $propertyAccessor,
        StringifierInterface $stringifier,
        array $callbacks = []
    ) {
        $this->em = $em;
        $this->localeProvider = $localeProvider;
        $this->propertyAccessor = $propertyAccessor;
        $this->stringifier = $stringifier;
        $this->callbacks = $callbacks;

        $this->globals = null;
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

        $globals      = $this->getGlobals();
        $properties   = array_unique($properties);
        $replacements = [];

        foreach ($properties as $property) {
            if (isset($globals[$property])) {
                $replacements[$this->getPlaceholder($property)] = $globals[$property];
            }
        }
        if (null !== $object) {
            foreach ($properties as $property) {
                $propertyCamelized = StringsUtil::toCamelCase($property);

                try {
                    $value = $this->propertyAccessor->getValue($object, $propertyCamelized);
                } catch (ExceptionInterface $ex) {
                    continue;
                } catch (TranslatableException $ex) {
                    continue;
                }

                $replacements[$this->getPlaceholder($property)] = $this->stringifier->stringify($value);
            }
        }
        if (!empty($replacements)) {
            $content = strtr($content, $replacements);
        }

        return $content;
    }

    /**
     * @param string $property Property
     *
     * @return string
     */
    private function getPlaceholder(string $property): string
    {
        return sprintf('%%%s%%', $property);
    }

    /**
     * @return array
     */
    private function getGlobals(): array
    {
        if (null === $this->globals) {
            $this->globals = $this->getGlobalPropertyRepository()->getValuesForPropertyEmbedder($this->localeProvider->getCurrentLocale());
        }

        return $this->globals;
    }

    /**
     * @return \Darvin\ContentBundle\Repository\GlobalPropertyRepository
     */
    private function getGlobalPropertyRepository(): GlobalPropertyRepository
    {
        return $this->em->getRepository(GlobalPropertyInterface::class);
    }
}
