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
     * @var \Darvin\ContentBundle\Entity\SlugMapItem
     *
     * @ORM\ManyToOne(targetEntity="Darvin\ContentBundle\Entity\SlugMapItem")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $tag;

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
     * @param \Darvin\ContentBundle\Entity\SlugMapItem $slug        Slug
     * @param string                                   $objectClass Object class
     * @param string                                   $objectId    Object ID
     * @param int                                      $value       Value
     * @param string|null                              $tag         Tag
     */
    public function __construct(SlugMapItem $slug, string $objectClass, string $objectId, int $value, ?string $tag = null)
    {
        $this->slug = $slug;
        $this->tag = $tag;
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
     * @return \Darvin\ContentBundle\Entity\SlugMapItem
     */
    public function getSlug(): SlugMapItem
    {
        return $this->slug;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
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
}
