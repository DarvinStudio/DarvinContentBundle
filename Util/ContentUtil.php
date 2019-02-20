<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Util;

/**
 * Content utility
 */
class ContentUtil
{
    /**
     * @param string|null $content     Content
     * @param string      $allowedTags Allowed tags
     *
     * @return bool
     */
    public static function isEmpty(?string $content, string $allowedTags = '<img></img>'): bool
    {
        if (null === $content) {
            return true;
        }

        $content = strip_tags($content, $allowedTags);

        if ('' === $content) {
            return true;
        }

        return '' === trim(str_replace("\xc2\xa0", ' ', $content));
    }
}
