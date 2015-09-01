<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Darvin\Utils\Mapping\Annotation as Darvin;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Metadata
 */
trait MetadataTrait
{
    use BaseMetadataTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Darvin\Slug
     */
    protected $slug;

    /**
     * @param string $slug slug
     *
     * @return MetadataTrait
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
}
