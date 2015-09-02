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
 * Content controller pool compiler pass
 */
class ContentControllerPoolPass implements CompilerPassInterface
{
    const TAG_CONTENT_CONTROLLER = 'darvin_content.controller';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $pool = $container->getDefinition('darvin_content.controller.pool');

        foreach ($container->findTaggedServiceIds(self::TAG_CONTENT_CONTROLLER) as $id => $attr) {
            $pool->addMethodCall('add', array(
                new Reference($id),
            ));
        }
    }
}