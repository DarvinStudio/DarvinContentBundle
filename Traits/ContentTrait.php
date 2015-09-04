<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Darvin\Utils\Mapping\Annotation as Darvin;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content
 */
trait ContentTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Darvin\DefaultValue(sourcePropertyPath="name")
     * @Darvin\Transliteratable
     */
    protected $slugSuffix;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param string $name name
     *
     * @return ContentTrait
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $slugSuffix slugSuffix
     *
     * @return ContentTrait
     */
    public function setSlugSuffix($slugSuffix)
    {
        $this->slugSuffix = $slugSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlugSuffix()
    {
        return $this->slugSuffix;
    }
}
