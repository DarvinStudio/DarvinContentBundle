<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

/**
 * Translation creator
 */
interface TranslationCreatorInterface
{
    /**
     * @param string        $targetLocale Target locale
     * @param callable|null $output       Output callback
     *
     * @throws \RuntimeException
     */
    public function createTranslations(string $targetLocale, ?callable $output = null): void;
}
