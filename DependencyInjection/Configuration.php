<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('darvin_content')
            ->children()
                ->arrayNode('widgets')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blacklist')->prototype('scalar')->end()->info('Blacklist of widget service IDs.')->end()
                        ->arrayNode('forward_to_controller')->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('controller')->isRequired()->end()
                                    ->arrayNode('sluggable_entity_classes')->prototype('scalar')->end()->end()
                                    ->arrayNode('options')->useAttributeAsKey('name')->prototype('scalar')->end()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('widget_factories')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blacklist')->prototype('scalar')->end()->info('Blacklist of widget factory service IDs.');

        return $treeBuilder;
    }
}
