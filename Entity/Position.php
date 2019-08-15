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
 */
class Position
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id
     */
    private $id;

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
