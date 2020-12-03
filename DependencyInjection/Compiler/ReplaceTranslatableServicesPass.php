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

use Darvin\ContentBundle\EventListener\TranslatableSubscriber;
use Darvin\ContentBundle\Traits\TranslatableTrait;
use Darvin\ContentBundle\Traits\TranslationTrait;
use Darvin\ContentBundle\Translatable\CurrentLocaleCallable;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replace translatable services compiler pass
 */
class ReplaceTranslatableServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ([
            'current_locale_callable.class' => CurrentLocaleCallable::class,
            'translatable_trait'            => TranslatableTrait::class,
            'translation_trait'             => TranslationTrait::class,
        ] as $suffix => $value) {
            $container->setParameter(sprintf('knp.doctrine_behaviors.translatable_subscriber.%s', $suffix), $value);
        }

        $container->getDefinition('knp.doctrine_behaviors.translatable_subscriber')
            ->setClass(TranslatableSubscriber::class)
            ->addArgument(new Reference('darvin_utils.orm.entity_resolver'));
    }
}
