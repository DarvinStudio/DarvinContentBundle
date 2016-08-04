<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
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
     */
    public function getTranslatableClass($entityClass);

    /**
     * @param string $entityClass Entity class
     *
     * @return string
     */
    public function getTranslationClass($entityClass);

    /**
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslatable($entityClass);

    /**
     * @param string $entityClass Entity class
     *
     * @return bool
     */
    public function isTranslation($entityClass);

    /**
     * @return string
     */
    public function getTranslationLocaleProperty();

    /**
     * @return string
     */
    public function getTranslationsProperty();
}
