<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle;

use Darvin\ContentBundle\DependencyInjection\Compiler\RegisterContentControllersPass;
use Darvin\ContentBundle\DependencyInjection\Compiler\RegisterWidgetFactoriesPass;
use Darvin\ContentBundle\DependencyInjection\Compiler\RegisterWidgetsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Content bundle
 */
class DarvinContentBundle extends Bundle
{
    public const MAJOR_VERSION = 9;

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container
            ->addCompilerPass(new RegisterContentControllersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new RegisterWidgetFactoriesPass())
            ->addCompilerPass(new RegisterWidgetsPass());
    }
}
