<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root */
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('property')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('embedder')->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('callbacks')->useAttributeAsKey('property')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('object')->isRequired()->cannotBeEmpty()
                                                ->validate()
                                                    ->ifTrue(function ($object): bool {
                                                        $object = (string)$object;

                                                        return !class_exists($object) && !interface_exists($object);
                                                    })
                                                    ->thenInvalid('Object %s does not exist.')
                                                ->end()
                                            ->end()
                                            ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('method')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function (array $callbacks): bool {
                                            $regex = '/^[0-9a-z_]+$/';

                                            foreach (array_keys($callbacks) as $property) {
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
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('canonical_url')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('parameter_whitelist')->useAttributeAsKey('pattern')->prototype('boolean')->end()
                            ->validate()
                                ->ifTrue(function (array $whitelist) {
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
                        ->arrayNode('forward_to_controller')->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('controller')->isRequired()->cannotBeEmpty()->end()
                                    ->arrayNode('sluggable_entity_classes')->prototype('scalar')->cannotBeEmpty()->end()->end()
                                    ->arrayNode('options')->useAttributeAsKey('name')->prototype('scalar')->end()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('widget_factory')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blacklist')->prototype('scalar')->cannotBeEmpty()->end()->info('Blacklist of widget factory service IDs.');

        return $builder;
    }
}
