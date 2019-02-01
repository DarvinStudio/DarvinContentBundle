<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * Translation
 */
trait TranslationTrait
{
    use Translation;

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        foreach (get_object_vars($this) as $property => $value) {
            if (in_array($property, ['translatable', 'locale'])) {
                continue;
            }
            if (!empty($value)) {
                return false;
            }
        }

        return true;
    }
}
