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
     * @param string|null $content Content
     *
     * @return bool
     */
    public function isEmpty(?string $content): bool
    {
        if (null === $content) {
            return true;
        }

        $content = strip_tags($content, '<img></img>');

        if ('' === $content) {
            return true;
        }

        return '' === trim(str_replace("\xc2\xa0", ' ', $content));
    }
}
