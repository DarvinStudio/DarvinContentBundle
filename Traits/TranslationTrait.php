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

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 */
trait TranslationTrait
{
    use \Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        foreach (get_object_vars($this) as $property => $value) {
            if (in_array($property, ['translatable', 'locale'])) {
                continue;
            }

            $empty = null === $value || '' === $value || (is_array($value) && empty($value));

            if (!$empty) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
