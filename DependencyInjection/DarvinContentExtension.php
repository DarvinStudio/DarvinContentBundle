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
        (new ConfigInjector($container))->inject($this->processConfiguration(new Configuration(), $configs), $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config'))->load([
            'canonical_url',
            'controller',
            'filterer',
            'form',
            'pagination',
            'router',
            'slug',
            'slug_map',
            'sorting',
            'translatable',
            'widget',
            'widget_factory',

            'dev/slug'         => ['env' => 'dev'],
            'dev/translatable' => ['env' => 'dev'],
            'dev/widget'       => ['env' => 'dev'],
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
