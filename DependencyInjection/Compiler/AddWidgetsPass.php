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
 * Add widgets compiler pass
 */
class AddWidgetsPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_content.widget.pool';

    const TAG_WIDGET = 'darvin_content.widget';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->addWidgets($container, array_keys($container->findTaggedServiceIds(self::TAG_WIDGET)));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container DI container
     * @param string[]                                                $ids       Service IDs
     */
    public function addWidgets(ContainerBuilder $container, array $ids)
    {
        if (empty($ids) || !$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $poolDefinition = $container->getDefinition(self::POOL_ID);

        foreach ($ids as $id) {
            $poolDefinition->addMethodCall('addWidget', [
                new Reference($id),
            ]);
        }
    }
}
