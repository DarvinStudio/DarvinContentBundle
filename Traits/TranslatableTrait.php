<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Darvin\ContentBundle\Translatable\TranslatableException;
use Doctrine\Common\Util\ClassUtils;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;

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
    public function __set($property, $value)
    {
        return $this->proxyCurrentLocaleTranslation('set'.ucfirst($property), [$value]);
    }

    /**
     * @param string $property Property name
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->proxyCurrentLocaleTranslation('get'.ucfirst($property));
    }

    /**
     * @param string $method Method name
     * @param array  $args   Method arguments
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return $this->proxyCurrentLocaleTranslation($method, $args);
    }

    /**
     * {@inheritdoc}
     */
    protected function proxyCurrentLocaleTranslation($method, array $arguments = [])
    {
        $translation = $this->translate($this->getCurrentLocale());

        if (!method_exists($translation, $method) && !preg_match('/^(get|is)/', $method)) {
            $method = 'get'.ucfirst($method);
        }
        if (!method_exists($translation, $method) && 0 === strpos($method, 'get')) {
            $method = substr_replace($method, 'is', 0, 3);
        }

        $callback = [$translation, $method];

        if (!method_exists($translation, $method)) {
            throw new TranslatableException(
                sprintf('Method "%s::%s()" does not exist.', ClassUtils::getClass($translation), $method)
            );
        }

        return call_user_func_array($callback, $arguments);
    }
}
