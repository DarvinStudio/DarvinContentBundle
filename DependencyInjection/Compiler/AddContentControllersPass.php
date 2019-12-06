<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection\Compiler;

use Darvin\ContentBundle\Controller\AbstractContentController;
use Darvin\ContentBundle\DependencyInjection\DarvinContentExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add content controllers compiler pass
 */
class AddContentControllersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $pool = $container->getDefinition('darvin_content.controller_pool');

        foreach (array_keys($container->findTaggedServiceIds(DarvinContentExtension::TAG_CONTROLLER)) as $id) {
            $controller = $container->getDefinition($id);

            if (in_array(AbstractContentController::class, class_parents($controller->getClass()))) {
                $controller->addMethodCall('setTwig', [new Reference('twig')]);
            }

            $pool->addMethodCall('addController', [new Reference($id)]);
        }
    }
}
