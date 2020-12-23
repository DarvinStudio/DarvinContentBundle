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
 * Content reference
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\SlugMapItemRepository")
 * @ORM\Table(name="content")
 */
class ContentReference
{
    /**
     * @var int|null
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
     * @var object|null
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
        $this->property = $property;

        $this->setObjectId($objectId);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug slug
     *
     * @return ContentReference
     */
    public function setSlug(string $slug): ContentReference
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    /**
     * @param string $objectClass objectClass
     *
     * @return ContentReference
     */
    public function setObjectClass(string $objectClass): ContentReference
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId(): string
    {
        return $this->objectId;
    }

    /**
     * @param mixed $objectId objectId
     *
     * @return ContentReference
     */
    public function setObjectId($objectId): ContentReference
    {
        $this->objectId = (string)$objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property property
     *
     * @return ContentReference
     */
    public function setProperty(string $property): ContentReference
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return object|null
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * @param object|null $object object
     *
     * @return ContentReference
     */
    public function setObject(?object $object): ContentReference
    {
        $this->object = $object;

        return $this;
    }
}
