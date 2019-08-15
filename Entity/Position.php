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
 * @ORM\Entity
 * @ORM\Table(name="content_position")
 */
class Position
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=36)
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
     * @param \Darvin\ContentBundle\Entity\SlugMapItem $slug Slug
     */
    public function __construct(SlugMapItem $slug)
    {
        $this->slug = $slug;
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
}
