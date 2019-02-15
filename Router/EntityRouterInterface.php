<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Router;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Entity router
 */
interface EntityRouterInterface
{
    /**
     * @param object|null $entity        Entity
     * @param int         $referenceType Reference type
     * @param array       $params        Parameters
     *
     * @return string|null
     */
    public function generateUrl($entity, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $params = []): ?string;
}
