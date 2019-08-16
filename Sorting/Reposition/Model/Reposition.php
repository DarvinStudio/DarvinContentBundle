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
     */
    private $slug;

    /**
     * @var string|null
     *
     * @Assert\Type("string")
     */
    private $tag;

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
     * @Assert\Count(min=1)
     */
    private $ids;

    /**
     * Reposition constructor.
     */
    public function __construct()
    {
        $this->ids = [];
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
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag tag
     *
     * @return Reposition
     */
    public function setTag(?string $tag): Reposition
    {
        $this->tag = $tag;

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
}