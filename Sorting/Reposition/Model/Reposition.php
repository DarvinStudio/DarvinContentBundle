<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting\Reposition\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reposition
 */
class Reposition
{
    /**
     * @var string|null
     *
     * @Assert\Type("string")
     * @Assert\Expression("null !== value or this.hasTags()")
     */
    private $slug;

    /**
     * @var array
     *
     * @Assert\Type("array")
     * @Assert\Expression("this.hasTags() or null !== this.getSlug()")
     */
    private $tags;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $class;

    /**
     * @var array
     *
     * @Assert\Type("array")
     * @Assert\All({@Assert\Type("string")})
     * @Assert\Count(min=1)
     */
    private $ids;

    /**
     * @var int|null
     *
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     */
    private $offset;

    /**
     * Reposition constructor.
     */
    public function __construct()
    {
        $this->tags = $this->ids = [];
    }

    /**
     * @return bool
     */
    public function hasTags(): bool
    {
        return !empty($this->tags);
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug slug
     *
     * @return Reposition
     */
    public function setSlug(?string $slug): Reposition
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags tags
     *
     * @return Reposition
     */
    public function setTags(array $tags): Reposition
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string $class class
     *
     * @return Reposition
     */
    public function setClass(?string $class): Reposition
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array $ids ids
     *
     * @return Reposition
     */
    public function setIds(array $ids): Reposition
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset offset
     *
     * @return Reposition
     */
    public function setOffset(?int $offset): Reposition
    {
        $this->offset = $offset;

        return $this;
    }
}
