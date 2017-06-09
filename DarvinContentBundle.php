<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle;

use Darvin\ContentBundle\DependencyInjection\Compiler\AddContentControllersPass;
use Darvin\ContentBundle\DependencyInjection\Compiler\AddFormNamesToCanonicalUrlWhitelist;
use Darvin\ContentBundle\DependencyInjection\Compiler\AddWidgetFactoriesPass;
use Darvin\ContentBundle\DependencyInjection\Compiler\AddWidgetsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Content bundle
 */
class DarvinContentBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new AddWidgetFactoriesPass())
            ->addCompilerPass(new AddWidgetsPass())
            ->addCompilerPass(new AddContentControllersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new AddFormNamesToCanonicalUrlWhitelist(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
