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
    /**
     * @param string $entityClass Entity class
     *
     * @return string
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function getTranslatableClass(string $entityClass): string;

    /**
     * @param string $entityClass Entity class
     *
     * @return string
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    public function getTranslationClass(string $entityClass): string;

    /**
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslatable(string $entityClass): bool;

    /**
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslation(string $entityClass): bool;

    /**
     * @return string
     */
    public function getTranslationLocaleProperty(): string;

    /**
     * @return string
     */
    public function getTranslationsProperty(): string;
}
