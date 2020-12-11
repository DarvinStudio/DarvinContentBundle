<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

/**
 * Translatable manager
 */
interface TranslatableManagerInterface
{
    public const TRANSLATION_LOCALE_PROPERTY = 'locale';
    public const TRANSLATIONS_PROPERTY       = 'translations';

    /**
     * @deprecated Use $entityClass::getTranslatableEntityClass() instead
     *
     * @param string $entityClass Entity class
     *
     * @return string
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function getTranslatableClass(string $entityClass): string;

    /**
     * @deprecated Use $entityClass::getTranslationEntityClass() instead
     *
     * @param string $entityClass Entity class
     *
     * @return string
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function getTranslationClass(string $entityClass): string;

    /**
     * @deprecated Check TranslatableInterface instead
     *
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslatable(string $entityClass): bool;

    /**
     * @deprecated Check TranslationInterface instead
     *
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslation(string $entityClass): bool;

    /**
     * @deprecated Use TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY constant instead
     *
     * @return string
     */
    public function getTranslationLocaleProperty(): string;

    /**
     * @deprecated Use TranslatableManagerInterface::TRANSLATIONS_PROPERTY constant instead
     *
     * @return string
     */
    public function getTranslationsProperty(): string;
}
