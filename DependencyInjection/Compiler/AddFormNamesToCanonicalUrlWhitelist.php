<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add form names to the canonical URL generator's request query parameter name whitelist
 */
class AddFormNamesToCanonicalUrlWhitelist implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $generator = $container->getDefinition('darvin_content.canonical_url.generator');

        foreach (array_keys($container->findTaggedServiceIds('form.type')) as $id) {
            if (0 !== strpos($container->getDefinition($id)->getClass(), 'App\\')) {
                continue;
            }
            try {
                /** @var \Symfony\Component\Form\FormTypeInterface $formType */
                $formType = $container->get($id);
            } catch (\Exception $ex) {
                continue;
            }

            $generator->addMethodCall('addQueryParamToWhitelist', [$formType->getBlockPrefix()]);
        }
    }
}
