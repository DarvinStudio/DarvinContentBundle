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
     * @var \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\Permission[]
     */
    private $requiredPermissions;

    /**
     * @param string                                                                $name                Name
     * @param string                                                                $service             Class or service ID
     * @param string|null                                                           $method              Method to call
     * @param array                                                                 $options             Options
     * @param \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\Permission[] $requiredPermissions Required permissions
     */
    public function __construct(string $name, string $service, ?string $method = null, array $options = [], array $requiredPermissions = [])
    {
        $this->name = $name;
        $this->service = $service;
        $this->method = $method;
        $this->options = $options;
        $this->requiredPermissions = $requiredPermissions;
    }

    /**
     * @param string $name     Option name
     * @param mixed  $fallback Fallback value
     *
     * @return mixed
     */
    public function getOption(string $name, $fallback = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $fallback;
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

    /**
     * @return \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\Permission[]
     */
    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
