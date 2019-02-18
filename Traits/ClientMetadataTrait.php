<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client metadata
 */
trait ClientMetadataTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $clientIp;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $userAgent;

    /**
     * @return string|null
     */
    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    /**
     * @param string|null $clientIp clientIp
     *
     * @return self
     */
    public function setClientIp(?string $clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent userAgent
     *
     * @return self
     */
    public function setUserAgent(?string $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
