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

use Darvin\Utils\DependencyInjection\ConfigInjector;
use Darvin\Utils\DependencyInjection\ConfigLoader;
use Darvin\Utils\DependencyInjection\ExtensionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DarvinContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config  = $this->processConfiguration(new Configuration(), $configs);
        $locales = $container->getParameter('locales');

        (new ConfigInjector($container))->inject($config, $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'canonical_url',
            'content',
            'controller',
            'filterer',
            'form',
            'orm',
            'pagination',
            'router',
            'slug',
            'slug_map',
            'translatable',
            'widget',
            'widget_factory',

            'dev/slug'         => ['env' => 'dev'],
            'dev/translatable' => ['env' => 'dev'],
            'dev/widget'       => ['env' => 'dev'],

            'locale/switch' => ['callback' => function () use ($locales) {
                return count($locales) > 1;
            }],

            'sorting' => ['callback' => function () use ($config) {
                return $config['sorting']['enabled'];
            }],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('knp_paginator');
    }
}
