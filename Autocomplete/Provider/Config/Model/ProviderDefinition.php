<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Autocomplete\Provider\Config\Model;

/**
 * Autocomplete provider definition
 */
class ProviderDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $service;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string      $name    Name
     * @param string      $service Class or service ID
     * @param string|null $method  Method to call
     * @param array       $options Options
     */
    public function __construct(string $name, string $service, ?string $method = null, array $options = [])
    {
        $this->name = $name;
        $this->service = $service;
        $this->method = $method;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
