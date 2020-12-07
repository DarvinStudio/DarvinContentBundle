<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection\Compiler;

use Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Disable KNP translatable event subscriber compiler pass
 */
class DisableKnpTranslatableSubscriberPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(TranslatableEventSubscriber::class)) {
            $container->getDefinition('darvin_content.translatable.event_subscriber.map')
                ->setDecoratedService(TranslatableEventSubscriber::class);
        }
    }
}
