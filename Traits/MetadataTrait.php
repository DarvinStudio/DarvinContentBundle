<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Darvin\Utils\Mapping\Annotation as Darvin;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Metadata
 */
trait MetadataTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Gedmo\Versioned
     */
    protected $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Gedmo\Versioned
     */
    protected $hidden;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     *
     * @Gedmo\Versioned
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Darvin\DefaultValue(sourcePropertyPath="title")
     *
     * @Gedmo\Versioned
     */
    protected $heading;

    /**
     * @var string
     *
     * @ORM\Column(length=1024)
     *
     * @Darvin\DefaultValue(sourcePropertyPath="title")
     *
     * @Gedmo\Versioned
     */
    protected $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Darvin\DefaultValue(sourcePropertyPath="title")
     *
     * @Gedmo\Versioned
     */
    protected $metaDescription;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->hidden = false;
    }

    /**
     * @return boolean
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled enabled
     *
     * @return self
     */
    public function setEnabled(?bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden hidden
     *
     * @return self
     */
    public function setHidden(?bool $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

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
     * @return self
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * @param string $heading heading
     *
     * @return self
     */
    public function setHeading(?string $heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle metaTitle
     *
     * @return self
     */
    public function setMetaTitle(?string $metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription metaDescription
     *
     * @return self
     */
    public function setMetaDescription(?string $metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
