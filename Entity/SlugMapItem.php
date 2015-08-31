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
    const CLASS_NAME = __CLASS__;

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
    private $entityClass;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $property;

    /**
     * @param string $slug        Slug
     * @param string $entityClass Entity class
     * @param string $entityId    Entity ID
     * @param string $property    Slug property
     */
    public function __construct($slug = null, $entityClass = null, $entityId = null, $property = null)
    {
        $this->slug = $slug;
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
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
     * @param string $entityClass entityClass
     *
     * @return SlugMapItem
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityId entityId
     *
     * @return SlugMapItem
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
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
