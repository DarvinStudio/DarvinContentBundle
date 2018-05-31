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
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Darvin\DefaultValue(sourcePropertyPath="title")
     *
     * @Gedmo\Versioned
     */
    protected $metaKeywords;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->hidden = false;
    }

    /**
     * @param boolean $enabled enabled
     *
     * @return MetadataTrait
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $hidden hidden
     *
     * @return MetadataTrait
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param string $title title
     *
     * @return MetadataTrait
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $heading heading
     *
     * @return MetadataTrait
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @param string $metaTitle metaTitle
     *
     * @return MetadataTrait
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaDescription metaDescription
     *
     * @return MetadataTrait
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaKeywords metaKeywords
     *
     * @return MetadataTrait
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
}
