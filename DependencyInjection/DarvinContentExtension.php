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

use Darvin\ContentBundle\Controller\ContentControllerInterface;
use Darvin\ContentBundle\Widget\WidgetFactoryInterface;
use Darvin\ContentBundle\Widget\WidgetInterface;
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
    public const TAG_CONTROLLER     = 'darvin_content.controller';
    public const TAG_WIDGET         = 'darvin_content.widget';
    public const TAG_WIDGET_FACTORY = 'darvin_content.widget_factory';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ContentControllerInterface::class)->addTag(self::TAG_CONTROLLER);
        $container->registerForAutoconfiguration(WidgetInterface::class)->addTag(self::TAG_WIDGET);
        $container->registerForAutoconfiguration(WidgetFactoryInterface::class)->addTag(self::TAG_WIDGET_FACTORY);

        $config  = $this->processConfiguration(new Configuration(), $configs);
        $locales = $container->getParameter('locales');

        (new ConfigInjector($container))->inject($config, $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'canonical_url',
            'content',
            'controller',
            'filterer',
            'form',
            'meta',
            'orm',
            'pagination',
            'property',
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
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure([
            'darvin_admin',
            'doctrine',
            'knp_paginator',
            'twig',
        ]);
    }
}
