<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
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
    const CANONICAL_URL_GENERATOR_ID = 'darvin_content.canonical_url.generator';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::CANONICAL_URL_GENERATOR_ID)) {
            return;
        }

        $canonicalUrlGeneratorDefinition = $container->getDefinition(self::CANONICAL_URL_GENERATOR_ID);

        foreach ($container->findTaggedServiceIds('form.type') as $id => $attr) {
            if (0 !== strpos($container->getDefinition($id)->getClass(), 'AppBundle\\')) {
                continue;
            }
            try {
                /** @var \Symfony\Component\Form\FormTypeInterface $formType */
                $formType = $container->get($id);
            } catch (\Exception $ex) {
                continue;
            }

            $canonicalUrlGeneratorDefinition->addMethodCall('addQueryParamToWhitelist', [$formType->getBlockPrefix()]);
        }
    }
}
