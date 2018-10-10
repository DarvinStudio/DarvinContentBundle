<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add widget factories to widget pool compiler pass
 */
class AddWidgetFactoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $blacklist = $container->getParameter('darvin_content.widget_factories.blacklist');
        $pool      = $container->getDefinition('darvin_content.widget.pool');

        foreach (array_keys($container->findTaggedServiceIds('darvin_content.widget_factory')) as $id) {
            if (!in_array($id, $blacklist)) {
                $pool->addMethodCall('addWidgetFactory', [new Reference($id)]);
            }
        }
    }
}
