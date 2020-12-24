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
use Darvin\Utils\Callback\CallbackRunnerInterface;
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
     * @var \Darvin\Utils\Callback\CallbackRunnerInterface
     */
    private $callbackRunner;

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
     * @param \Darvin\Utils\Callback\CallbackRunnerInterface              $callbackRunner   Callback runner
     * @param \Doctrine\ORM\EntityManagerInterface                        $em               Entity manager
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                $localeProvider   Locale provider
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     * @param \Darvin\Utils\Strings\Stringifier\StringifierInterface      $stringifier      Stringifier
     * @param array                                                       $callbacks        Callbacks
     */
    public function __construct(
        CallbackRunnerInterface $callbackRunner,
        EntityManagerInterface $em,
        LocaleProviderInterface $localeProvider,
        PropertyAccessorInterface $propertyAccessor,
        StringifierInterface $stringifier,
        array $callbacks = []
    ) {
        $this->callbackRunner = $callbackRunner;
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

        if (empty($matches[1])) {
            return $content;
        }

        $properties = $lowerProperties = [];

        foreach ($matches[1] as $property) {
            $lowerProperty = strtolower($property);

            $properties[$property] = $lowerProperties[$lowerProperty] = $lowerProperty;
        }

        $globals = $this->getGlobals();
        $values  = [];

        foreach ($lowerProperties as $property) {
            if (isset($globals[$property])) {
                $values[$property] = $globals[$property];
            }
        }
        if (null !== $object) {
            $callbacks = $this->getObjectCallbacks($object);

            foreach ($lowerProperties as $property) {
                if (isset($callbacks[$property])) {
                    $values[$property] = $this->callbackRunner->runCallback(
                        $callbacks[$property]['service'],
                        $callbacks[$property]['method'],
                        $object
                    );

                    continue;
                }

                $propertyCamelized = StringsUtil::toCamelCase($property);

                try {
                    $value = $this->propertyAccessor->getValue($object, $propertyCamelized);
                } catch (ExceptionInterface | TranslatableException $ex) {
                    continue;
                }

                $values[$property] = $value;
            }
        }

        $values       = array_map([$this->stringifier, 'stringify'], $values);
        $replacements = [];

        foreach ($properties as $property => $lowerProperty) {
            $replacements[sprintf('%%%s%%', $property)] = $values[$lowerProperty] ?? '';
        }

        $content = strtr($content, $replacements);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        return $content;
    }

    /**
     * @param object $object Object
     *
     * @return array
     */
    private function getObjectCallbacks(object $object): array
    {
        $objectCallbacks = [];

        foreach ($this->callbacks as $class => $callbacks) {
            if ($object instanceof $class) {
                $objectCallbacks = array_merge($objectCallbacks, $callbacks);
            }
        }

        return $objectCallbacks;
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
