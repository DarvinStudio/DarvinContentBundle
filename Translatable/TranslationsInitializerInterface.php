<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

/**
 * Translations initializer
 */
interface TranslationsInitializerInterface
{
    /**
     * @param object $entity  Entity
     * @param array  $locales Locales to create translations for
     */
    public function initializeTranslations($entity, array $locales);
}
