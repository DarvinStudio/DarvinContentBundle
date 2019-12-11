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
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property embedder
 */
class PropertyEmbedder implements PropertyEmbedderInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

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
     * @param \Psr\Container\ContainerInterface                           $container        Service container
     * @param \Doctrine\ORM\EntityManagerInterface                        $em               Entity manager
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                $localeProvider   Locale provider
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     * @param \Darvin\Utils\Strings\Stringifier\StringifierInterface      $stringifier      Stringifier
     * @param array                                                       $callbacks        Callbacks
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em,
        LocaleProviderInterface $localeProvider,
        PropertyAccessorInterface $propertyAccessor,
        StringifierInterface $stringifier,
        array $callbacks = []
    ) {
        $this->container = $container;
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
                    $values[$property] = $this->call($callbacks[$property], $object);

                    continue;
                }

                $propertyCamelized = StringsUtil::toCamelCase($property);

                try {
                    $value = $this->propertyAccessor->getValue($object, $propertyCamelized);
                } catch (ExceptionInterface $ex) {
                    continue;
                } catch (TranslatableException $ex) {
                    continue;
                }

                $values[$property] = $value;
            }
        }
        if (empty($values)) {
            return $content;
        }

        $values       = array_map([$this->stringifier, 'stringify'], $values);
        $replacements = [];

        foreach ($properties as $property => $lowerProperty) {
            if (isset($values[$lowerProperty])) {
                $replacements[sprintf('%%%s%%', $property)] = $values[$lowerProperty];
            }
        }
        if (empty($replacements)) {
            return $content;
        }

        return strtr($content, $replacements);
    }

    /**
     * @param array  $callback Callback
     * @param object $object   Object
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function call(array $callback, object $object)
    {
        $id     = $callback['service'];
        $method = $callback['method'];

        if ($this->container->has($id)) {
            $service = $this->container->get($id);

            if (null === $method) {
                if (!is_callable($service)) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" is not callable. Make sure it has "__invoke()" method.', get_class($service)));
                }

                return $service($object);
            }
            if (!method_exists($service, $method)) {
                throw new \InvalidArgumentException(sprintf('Method "%s::%s()" does not exist.', get_class($service), $method));
            }

            return $service->$method($object);
        }
        if (!class_exists($id)) {
            throw new \InvalidArgumentException(
                sprintf('Service or class "%s" does not exist. If it is a service, make sure it is public.', $id)
            );
        }
        if (null === $method) {
            throw new \InvalidArgumentException('Method not specified.');
        }
        if (!method_exists($id, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s()" does not exist.', $id, $method));
        }

        $callable = [$id, $method];

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s()" is not statically callable.', $id, $method));
        }

        return $callable($object);
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
