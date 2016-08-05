<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Translatable current locale callable
 */
class CurrentLocaleCallable
{
    const CURRENT_LOCALE_CALLABLE_CLASS = __CLASS__;

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
    public function __invoke()
    {
        $request = $this->getRequestStack()->getCurrentRequest();

        return !empty($request) ? $request->getLocale() : null;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    private function getRequestStack()
    {
        return $this->container->get('request_stack');
    }
}
