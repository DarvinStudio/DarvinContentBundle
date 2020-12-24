<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

/**
 * Content reference object loader
 */
interface ContentReferenceObjectLoaderInterface
{
    /**
     * @param \Darvin\ContentBundle\Entity\ContentReference[]|\Darvin\ContentBundle\Entity\ContentReference $references Content references
     */
    public function loadObjects($references): void;
}
