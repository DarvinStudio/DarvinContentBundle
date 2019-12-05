<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Darvin\ContentBundle\Translatable\TranslatableException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Translatable
 */
trait TranslatableTrait
{
    use Translatable;

    /**
     * @param string $property Property name
     * @param mixed  $value    Property value
     *
     * @return mixed
     */
    public function __set(string $property, $value)
    {
        return $this->proxyCurrentLocaleTranslation('set'.ucfirst($property), [$value]);
    }

    /**
     * @param string $property Property name
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->proxyCurrentLocaleTranslation('get'.ucfirst($property));
    }

    /**
     * @param string $method Method name
     * @param array  $args   Method arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->proxyCurrentLocaleTranslation($method, $args);
    }

    /**
     * @Assert\Valid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations = $this->translations ?: new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    protected function proxyCurrentLocaleTranslation(string $method, array $arguments = [])
    {
        $translation = $this->translate($this->getCurrentLocale());

        $getter = $isser = $method;

        if (!method_exists($translation, $method) && !preg_match('/^(get|is)/', $method)) {
            $method = $getter = sprintf('get%s', ucfirst($method));
        }
        if (!method_exists($translation, $method) && 0 === strpos($method, 'get')) {
            $method = $isser = substr_replace($method, 'is', 0, 3);
        }
        if (!method_exists($translation, $method)) {
            if ($getter === $isser) {
                throw new TranslatableException(sprintf(
                    'Method "%s()" does not exist in class "%s" and it\'s translation "%s".',
                    $getter,
                    ClassUtils::getClass($this),
                    ClassUtils::getClass($translation)
                ));
            }

            throw new TranslatableException(sprintf(
                'Methods "%s()" and "%s()" not exist in class "%s" and it\'s translation "%s".',
                $getter,
                $isser,
                ClassUtils::getClass($this),
                ClassUtils::getClass($translation)
            ));
        }

        return call_user_func_array([$translation, $method], $arguments);
    }
}
