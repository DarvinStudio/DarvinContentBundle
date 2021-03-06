<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('darvin_content');

        $builder->getRootNode()
            ->children()
                ->arrayNode('autocomplete')->canBeEnabled()
                    ->children()
                        ->arrayNode('providers')->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('method')->defaultNull()->end()
                                    ->arrayNode('options')->useAttributeAsKey('name')->prototype('scalar')->end()->end()
                                    ->arrayNode('required_permissions')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('attribute')->isRequired()->cannotBeEmpty()->end()
                                                ->scalarNode('subject')->defaultNull()->end()
                                            ->end()
                                            ->beforeNormalization()->ifString()->then(function (string $attribute): array {
                                                return [
                                                    'attribute' => $attribute,
                                                ];
                                            })->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->beforeNormalization()->ifString()->then(function (string $service): array {
                                    return [
                                        'service' => $service,
                                    ];
                                })->end()
                                ->beforeNormalization()->ifArray()->then(function (array $provider): array {
                                    if (isset($provider['entity'], $provider['repository_method'])) {
                                        $provider = array_merge($provider, [
                                            'service' => 'darvin_content.autocomplete.provider.repository',
                                            'options' => [
                                                'entity'            => $provider['entity'],
                                                'repository_method' => $provider['repository_method'],
                                            ],
                                        ]);

                                        unset($provider['entity'], $provider['repository_method']);

                                        return $provider;
                                    }
                                    if (1 === count($provider)) {
                                        $key = key($provider);

                                        if (!in_array($key, ['service', 'method', 'options', 'required_permissions'])) {
                                            return [
                                                'service' => $key,
                                                'method'  => $provider[$key],
                                            ];
                                        }
                                    }

                                    return $provider;
                                })->end()
                            ->end()
                            ->validate()
                                ->ifTrue(function (array $providers): bool {
                                    $regex = '/^[0-9a-z_]+$/';

                                    foreach (array_keys($providers) as $name) {
                                        if (!preg_match($regex, $name)) {
                                            throw new \InvalidArgumentException(
                                                sprintf('Autocomplete provider name "%s" does not match regex "%s".', $name, $regex)
                                            );
                                        }
                                    }

                                    return false;
                                })
                                ->thenInvalid('')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('property')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('embedder')->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('callbacks')->useAttributeAsKey('object')
                                    ->prototype('array')->useAttributeAsKey('property')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
                                                ->scalarNode('method')->defaultNull()->end()
                                            ->end()
                                            ->beforeNormalization()->ifString()->then(function (string $service): array {
                                                return [
                                                    'service' => $service,
                                                ];
                                            })
                                            ->end()
                                            ->beforeNormalization()->ifArray()->then(function (array $callback): array {
                                                if (1 === count($callback)) {
                                                    $key = key($callback);

                                                    if (!in_array($key, ['service', 'method'])) {
                                                        return [
                                                            'service' => $key,
                                                            'method'  => $callback[$key],
                                                        ];
                                                    }
                                                }

                                                return $callback;
                                            })
                                            ->end()
                                        ->end()
                                        ->validate()
                                            ->ifTrue(function (array $properties): bool {
                                                $regex = '/^[0-9a-z_]+$/';

                                                foreach (array_keys($properties) as $property) {
                                                    if (!preg_match($regex, $property)) {
                                                        throw new \InvalidArgumentException(
                                                            sprintf('Property "%s" does not match regex "%s".', $property, $regex)
                                                        );
                                                    }
                                                }

                                                return false;
                                            })
                                            ->thenInvalid('')
                                        ->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function (array $objects): bool {

                                            foreach (array_keys($objects) as $object) {
                                                if (!class_exists($object) && !interface_exists($object)) {
                                                    throw new \InvalidArgumentException(sprintf('Object "%s" does not exist.', $object));
                                                }

                                            }

                                            return false;
                                        })
                                        ->thenInvalid('')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('canonical_url')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('parameter_whitelist')->useAttributeAsKey('pattern')->prototype('boolean')->end()
                            ->validate()
                                ->ifTrue(function (array $whitelist): bool {
                                    foreach (array_keys($whitelist) as $pattern) {
                                        $pattern = (string)$pattern;

                                        if (false === @preg_match(sprintf('/^%s$/', $pattern), '')) {
                                            throw new \InvalidArgumentException(sprintf('"%s" is not valid pattern.', $pattern));
                                        }
                                    }

                                    return false;
                                })
                                ->thenInvalid('')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('sorting')->canBeEnabled()
                    ->children()
                        ->arrayNode('reposition')->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('required_permissions')->prototype('scalar')->end()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('widget')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blacklist')->prototype('scalar')->cannotBeEmpty()->end()->info('Blacklist of widget names or service IDs.')->end()
                    ->end()
                ->end()
                ->arrayNode('widget_factory')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blacklist')->prototype('scalar')->cannotBeEmpty()->end()->info('Blacklist of widget factory service IDs.');

        return $builder;
    }
}
