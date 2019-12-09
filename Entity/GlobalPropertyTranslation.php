<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Entity;

use Darvin\ContentBundle\Traits\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Global property translation
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="content_global_property_translation")
 */
class GlobalPropertyTranslation
{
    use TranslationTrait;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    protected $value;

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title title
     *
     * @return GlobalPropertyTranslation
     */
    public function setTitle(?string $title): GlobalPropertyTranslation
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value value
     *
     * @return GlobalPropertyTranslation
     */
    public function setValue(?string $value): GlobalPropertyTranslation
    {
        $this->value = $value;

        return $this;
    }
}
