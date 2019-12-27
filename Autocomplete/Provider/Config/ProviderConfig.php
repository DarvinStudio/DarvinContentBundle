<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Autocomplete\Provider\Config;

use Darvin\ContentBundle\Autocomplete\Provider\Config\Model\Permission;
use Darvin\ContentBundle\Autocomplete\Provider\Config\Model\ProviderDefinition;

/**
 * Autocomplete provider configuration
 */
class ProviderConfig implements ProviderConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\ProviderDefinition[]|null
     */
    private $providers;

    /**
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->providers = null;
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider(string $name): ProviderDefinition
    {
        if (!$this->hasProvider($name)) {
            throw new \InvalidArgumentException(sprintf('Autocomplete provider "%s" does not exist.', $name));
        }

        return $this->getProviders()[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function hasProvider(string $name): bool
    {
        $providers = $this->getProviders();

        return isset($providers[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderNames(): array
    {
        return array_keys($this->getProviders());
    }

    /**
     * @return \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\ProviderDefinition[]
     */
    private function getProviders(): array
    {
        if (null === $this->providers) {
            $providers = [];

            foreach ($this->config as $name => $attr) {
                $name = (string)$name;

                $providers[$name] = new ProviderDefinition(
                    $name,
                    (string)$attr['service'],
                    null !== $attr['method'] ? (string)$attr['method'] : null,
                    $attr['options'],
                    array_map(function (array $permission): Permission {
                        return new Permission($permission['attribute'], $permission['subject']);
                    }, $attr['required_permissions'])
                );
            }

            $this->providers = $providers;
        }

        return $this->providers;
    }
}
