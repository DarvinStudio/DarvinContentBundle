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

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Darvin\ContentBundle\Translatable\CurrentLocaleCallable;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DarvinContentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ([
            'controller',
            'filterer',
            'slug',
            'sorting',
            'translatable',
            'widget',
            'widget_factory',
        ] as $resource) {
            $loader->load($resource.'.yml');
        }

        $container->setParameter('darvin_content.widgets.forward_to_controller', $config['widgets']['forward_to_controller']);

        $container->setParameter('knp.doctrine_behaviors.translatable_subscriber.current_locale_callable.class', CurrentLocaleCallable::class);
        $container->setParameter('knp.doctrine_behaviors.translatable_subscriber.translatable_trait', TranslatableTrait::class);
    }
}
