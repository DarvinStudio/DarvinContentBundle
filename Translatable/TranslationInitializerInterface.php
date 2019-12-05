<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

/**
 * Translation initializer
 */
interface TranslationInitializerInterface
{
    /**
     * @param object $entity  Entity
     * @param array  $locales Locales to create translations for
     *
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function initializeTranslations(object $entity, array $locales): void;
}
