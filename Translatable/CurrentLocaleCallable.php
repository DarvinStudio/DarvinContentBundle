<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Darvin\Utils\Locale\LocaleProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Translatable current locale callable
 */
class CurrentLocaleCallable
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container DI container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        return $this->getLocaleProvider()->getCurrentLocale();
    }

    /**
     * @return \Darvin\Utils\Locale\LocaleProviderInterface
     */
    private function getLocaleProvider(): LocaleProviderInterface
    {
        return $this->container->get('darvin_utils.locale.provider');
    }
}
