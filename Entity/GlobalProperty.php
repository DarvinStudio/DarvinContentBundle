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

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Global property
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\GlobalPropertyRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="content_global_property")
 *
 * @DoctrineAssert\UniqueEntity(fields={"name"})
 *
 * @method string getTitle()
 * @method string getValue()
 */
class GlobalProperty implements GlobalPropertyInterface
{
    use TranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Regex("/^\w+$/")
     */
    protected $name;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getTitle();
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name name
     *
     * @return GlobalProperty
     */
    public function setName(?string $name): GlobalProperty
    {
        $this->name = $name;

        return $this;
    }
}
