<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add content controllers compiler pass
 */
class AddContentControllersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $containerRef = new Reference('service_container');
        $pool         = $container->getDefinition('darvin_content.controller.pool');

        foreach (array_keys($container->findTaggedServiceIds('darvin_content.controller')) as $id) {
            $controller = $container->getDefinition($id);

            if (in_array(AbstractController::class, class_parents($controller->getClass()))) {
                $controller->addMethodCall('setContainer', [$containerRef]);
            }

            $pool->addMethodCall('addController', [new Reference($id)]);
        }
    }
}
