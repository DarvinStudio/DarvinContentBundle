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

use Knp\DoctrineBehaviors\Model\Translatable\Translatable;

/**
 * Translatable
 */
trait TranslatableTrait
{
    use Translatable;

    /**
     * {@inheritdoc}
     */
    protected function proxyCurrentLocaleTranslation($method, array $arguments = [])
    {
        $translation = $this->translate($this->getCurrentLocale());

        if (!method_exists($translation, $method) && 0 === strpos($method, 'get')) {
            $method = substr_replace($method, 'is', 0, 3);
        }

        return call_user_func_array(array($translation, $method), $arguments);
    }
}
