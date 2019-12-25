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

use Darvin\ContentBundle\Autocomplete\Provider\Config\Model\ProviderDefinition;

/**
 * Autocomplete provider configuration
 */
interface ProviderConfigInterface
{
    /**
     * @param string $name Autocomplete provider name
     *
     * @return \Darvin\ContentBundle\Autocomplete\Provider\Config\Model\ProviderDefinition
     * @throws \InvalidArgumentException
     */
    public function getProvider(string $name): ProviderDefinition;

    /**
     * @param string $name Autocomplete provider name
     *
     * @return bool
     */
    public function hasProvider(string $name): bool;

    /**
     * @return string[]
     */
    public function getProviderNames(): array;
}