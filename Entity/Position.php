<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Position
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\PositionRepository")
 * @ORM\Table(name="content_position")
 */
class Position
{
    /**
     * @var string
     *
     * @ORM\Column(length=36)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \Darvin\ContentBundle\Entity\ContentReference
     *
     * @ORM\ManyToOne(targetEntity="Darvin\ContentBundle\Entity\ContentReference")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $contentReference;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $tags;

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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @param \Darvin\ContentBundle\Entity\ContentReference $contentReference Content reference
     * @param string                                        $objectClass      Object class
     * @param string                                        $objectId         Object ID
     * @param int                                           $value            Value
     * @param array                                         $tags             Tags
     */
    public function __construct(ContentReference $contentReference, string $objectClass, string $objectId, int $value, array $tags = [])
    {
        $this->contentReference = $contentReference;
        $this->tags = $tags;
        $this->objectClass = $objectClass;
        $this->objectId = $objectId;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return \Darvin\ContentBundle\Entity\ContentReference
     */
    public function getContentReference(): ContentReference
    {
        return $this->contentReference;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    /**
     * @return string
     */
    public function getObjectId(): string
    {
        return $this->objectId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value value
     *
     * @return Position
     */
    public function setValue(int $value): Position
    {
        $this->value = $value;

        return $this;
    }
}
