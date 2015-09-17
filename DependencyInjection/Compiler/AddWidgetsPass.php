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

use Darvin\Utils\DependencyInjection\TaggedServiceIdsSorter;
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
        if (!$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $widgetIds = $container->findTaggedServiceIds(self::TAG_WIDGET);

        if (empty($widgetIds)) {
            return;
        }

        $sorter = new TaggedServiceIdsSorter();
        $sorter->sort($widgetIds);

        $poolDefinition = $container->getDefinition(self::POOL_ID);

        foreach ($widgetIds as $id => $attr) {
            $poolDefinition->addMethodCall('add', array(
                new Reference($id),
            ));
        }
    }
}
