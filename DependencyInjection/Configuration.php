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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('darvin_content');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root */
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('canonical_url')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('parameter_whitelist')
                            ->prototype('scalar')->cannotBeEmpty()
                                ->validate()
                                    ->ifTrue(function ($pattern) {
                                        return false === @preg_match('/^'.$pattern.'$/', null);
                                    })
                                    ->thenInvalid('%s is not valid pattern.')
                                ->end()
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
