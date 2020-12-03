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

use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Translatable locale setter
 */
interface TranslatableLocaleSetterInterface
{
    /**
     * @param \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface $translatable Translatable
     *
     * @return \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface
     */
    public function setLocales(TranslatableInterface $translatable): TranslatableInterface;
}
