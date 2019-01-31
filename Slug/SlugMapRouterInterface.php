<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Slug;

use Darvin\ContentBundle\Entity\SlugMapItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Slug map router
 */
interface SlugMapRouterInterface
{
    /**
     * @param \Darvin\ContentBundle\Entity\SlugMapItem|null $item          Slug map item
     * @param int                                           $referenceType Reference type
     * @param array                                         $params        Parameters
     *
     * @return string|null
     */
    public function generateUrl(?SlugMapItem $item = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $params = []): ?string;
}
