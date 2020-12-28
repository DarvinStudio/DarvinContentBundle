<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Router;

use Darvin\ContentBundle\Entity\ContentReference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Content reference router
 */
interface ContentReferenceRouterInterface
{
    /**
     * @param \Darvin\ContentBundle\Entity\ContentReference|null $contentReference Content reference
     * @param int                                                $referenceType    Reference type
     * @param array                                              $parameters       Parameters
     *
     * @return string|null
     */
    public function generateUrl(?ContentReference $contentReference, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $parameters = []): ?string;
}
