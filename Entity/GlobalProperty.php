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

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Global property
 *
 * @ORM\Entity(repositoryClass="Darvin\ContentBundle\Repository\GlobalPropertyRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="content_global_property")
 */
class GlobalProperty implements GlobalPropertyInterface
{
    use TranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
