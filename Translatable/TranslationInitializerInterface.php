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

use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Translation initializer
 */
interface TranslationInitializerInterface
{
    /**
     * @param \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface $translatable Translatable
     * @param string[]|null                                                $locales      Locales to create translations for
     */
    public function initializeTranslations(TranslatableInterface $translatable, ?array $locales = null): void;
}
