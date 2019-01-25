<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget\Embedder\Exception;

/**
 * Widget embedder redirect exception
 */
class RedirectException extends WidgetEmbedderException
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @param string $url     URL
     * @param int    $status  Status
     * @param array  $headers Headers
     */
    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        parent::__construct($url, $status);

        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
