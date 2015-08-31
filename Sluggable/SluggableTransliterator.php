<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sluggable;

use Darvin\Utils\Strings\Transliterator\TransliteratorInterface;

/**
 * Sluggable transliterator
 */
class SluggableTransliterator
{
    /**
     * @var \Darvin\Utils\Strings\Transliterator\TransliteratorInterface
     */
    private $transliterator;

    /**
     * @param \Darvin\Utils\Strings\Transliterator\TransliteratorInterface $transliterator Transliterator
     */
    public function __construct(TransliteratorInterface $transliterator)
    {
        $this->transliterator = $transliterator;
    }

    /**
     * @param string $string String to transliterate
     *
     * @return string
     */
    public function transliterate($string)
    {
        return $this->transliterator->transliterate($string);
    }
}
