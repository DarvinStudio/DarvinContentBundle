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
    const TAG_CONTROLLER = 'darvin_content.controller';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $controllerIds = $container->findTaggedServiceIds(self::TAG_CONTROLLER);

        if (empty($controllerIds)) {
            return;
        }

        $poolDefinition = $container->getDefinition('darvin_content.controller.pool');

        foreach ($controllerIds as $id => $attr) {
            $poolDefinition->addMethodCall('add', array(
                new Reference($id),
            ));
        }
    }
}
