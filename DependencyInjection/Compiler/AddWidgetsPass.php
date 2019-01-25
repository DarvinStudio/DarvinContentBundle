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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add widgets compiler pass
 */
class AddWidgetsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $blacklist = $container->getParameter('darvin_content.widget.blacklist');
        $pool      = $container->getDefinition('darvin_content.widget.pool');

        foreach (array_keys($container->findTaggedServiceIds('darvin_content.widget')) as $id) {
            if (!in_array($id, $blacklist)) {
                $pool->addMethodCall('addWidget', [new Reference($id)]);
            }
        }
    }
}
