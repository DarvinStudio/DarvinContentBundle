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

use Doctrine\ORM\Mapping as ORM;

/**
 * Client metadata
 */
trait ClientMetadataTrait
{
    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $clientIp;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $userAgent;

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp clientIp
     *
     * @return ClientMetadataTrait
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent userAgent
     *
     * @return ClientMetadataTrait
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
