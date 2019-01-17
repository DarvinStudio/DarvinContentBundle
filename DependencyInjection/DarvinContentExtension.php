<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection;

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Darvin\ContentBundle\Translatable\CurrentLocaleCallable;
use Darvin\Utils\DependencyInjection\ConfigInjector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

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
    public function load(array $configs, ContainerBuilder $container)
    {
        (new ConfigInjector())->inject($this->processConfiguration(new Configuration(), $configs), $container, $this->getAlias());

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ([
            'canonical_url',
            'controller',
            'filterer',
            'form',
            'pagination',
            'slug',
            'sorting',
            'translatable',
            'widget',
            'widget_factory',
        ] as $resource) {
            $loader->load($resource.'.yaml');
        }
        if ('dev' === $container->getParameter('kernel.environment')) {
            foreach ([
                'slug',
                'translatable',
                'widget',
            ] as $resource) {
                $loader->load(sprintf('dev/%s.yaml', $resource));
            }
        }

        $container->setParameter('knp.doctrine_behaviors.translatable_subscriber.current_locale_callable.class', CurrentLocaleCallable::class);
        $container->setParameter('knp.doctrine_behaviors.translatable_subscriber.translatable_trait', TranslatableTrait::class);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config/app');

        foreach ([
            'knp_paginator',
        ] as $extension) {
            if ($container->hasExtension($extension)) {
                $container->prependExtensionConfig($extension, Yaml::parse(file_get_contents($fileLocator->locate($extension.'.yaml')))[$extension]);
            }
        }
    }
}
