<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting\Reposition;

use Darvin\ContentBundle\Sorting\Reposition\Model\Reposition;

/**
 * Repositioner
 */
interface RepositionerInterface
{
    /**
     * @param \Darvin\ContentBundle\Sorting\Reposition\Model\Reposition $reposition Reposition
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function reposition(Reposition $reposition): void;
}
