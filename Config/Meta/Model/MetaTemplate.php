<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Config\Meta\Model;

/**
 * Configuration meta template model
 */
class MetaTemplate
{
    /**
     * @var string|null
     */
    private $heading;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @return string|null
     */
    public function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * @param string|null $heading heading
     *
     * @return MetaTemplate
     */
    public function setHeading(?string $heading): MetaTemplate
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title title
     *
     * @return MetaTemplate
     */
    public function setTitle(?string $title): MetaTemplate
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description description
     *
     * @return MetaTemplate
     */
    public function setDescription(?string $description): MetaTemplate
    {
        $this->description = $description;

        return $this;
    }
}
