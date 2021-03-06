<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
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
}
