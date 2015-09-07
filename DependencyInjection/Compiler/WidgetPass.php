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
 * Widget compiler pass
 */
class WidgetPass implements CompilerPassInterface
{
    const TAG_WIDGET = 'darvin_content.widget';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $widgetTwigExtension = $container->getDefinition('darvin_content.widget.twig_extension');

        foreach ($container->findTaggedServiceIds(self::TAG_WIDGET) as $id => $attr) {
            $widgetTwigExtension->addMethodCall('addWidget', array(
                new Reference($id),
            ));
        }
    }
}
