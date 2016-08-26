<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
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
 * Add content controllers compiler pass
 */
class AddContentControllersPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_content.controller.pool';

    const TAG_CONTENT_CONTROLLER = 'darvin_content.controller';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $controllerIds = $container->findTaggedServiceIds(self::TAG_CONTENT_CONTROLLER);

        if (empty($controllerIds)) {
            return;
        }

        $poolDefinition = $container->getDefinition(self::POOL_ID);

        $containerReference = new Reference('service_container');

        foreach ($controllerIds as $id => $attr) {
            $controllerDefinition = $container->getDefinition($id);

            if (in_array('Symfony\Component\DependencyInjection\ContainerAwareInterface', class_implements($controllerDefinition->getClass()))) {
                $controllerDefinition->addMethodCall('setContainer', [
                    $containerReference,
                ]);
            }

            $poolDefinition->addMethodCall('addController', [
                new Reference($id),
            ]);
        }
    }
}
