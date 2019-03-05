<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Entity;

use Darvin\Utils\Mapping\Annotation as Darvin;
use Doctrine\ORM\Mapping as ORM;

/**
 * Slug map item
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\SlugMapItemRepository")
 * @ORM\Table(name="content_slug_map")
 */
class SlugMapItem
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=2550)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $objectClass;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $property;

    /**
     * @var object
     *
     * @Darvin\CustomObject(classPropertyPath="objectClass", initPropertyValuePath="objectId")
     */
    private $object;

    /**
     * @param string $slug        Slug
     * @param string $objectClass Object class
     * @param mixed  $objectId    Object ID
     * @param string $property    Slug property
     */
    public function __construct(string $slug, string $objectClass, $objectId, string $property)
    {
        $this->slug = $slug;
        $this->objectClass = $objectClass;
        $this->objectId = (string)$objectId;
        $this->property = $property;
    }

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
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug slug
     *
     * @return SlugMapItem
     */
    public function setSlug(?string $slug): SlugMapItem
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectClass(): ?string
    {
        return $this->objectClass;
    }

    /**
     * @param string $objectClass objectClass
     *
     * @return SlugMapItem
     */
    public function setObjectClass(?string $objectClass): SlugMapItem
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    /**
     * @param mixed $objectId objectId
     *
     * @return SlugMapItem
     */
    public function setObjectId($objectId): SlugMapItem
    {
        $this->objectId = (string)$objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * @param string $property property
     *
     * @return SlugMapItem
     */
    public function setProperty(?string $property): SlugMapItem
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object $object object
     *
     * @return SlugMapItem
     */
    public function setObject($object): SlugMapItem
    {
        $this->object = $object;

        return $this;
    }
}
