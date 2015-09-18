<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slug map item
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\SlugMapItemRepository")
 * @ORM\Table(name="slug_map")
 */
class SlugMapItem
{
    const SLUG_MAP_ITEM_CLASS = __CLASS__;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2550)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $objectClass;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $property;

    /**
     * @param string $slug        Slug
     * @param string $objectClass Object class
     * @param string $objectId    Object ID
     * @param string $property    Slug property
     */
    public function __construct($slug = null, $objectClass = null, $objectId = null, $property = null)
    {
        $this->slug = $slug;
        $this->objectClass = $objectClass;
        $this->objectId = $objectId;
        $this->property = $property;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $slug slug
     *
     * @return SlugMapItem
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $objectClass objectClass
     *
     * @return SlugMapItem
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * @param string $objectId objectId
     *
     * @return SlugMapItem
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $property property
     *
     * @return SlugMapItem
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
