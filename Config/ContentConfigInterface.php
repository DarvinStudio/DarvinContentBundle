<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Config;

/**
 * Content config
 */
interface ContentConfigInterface
{
    /**
     * @return string|null
     */
    public function getMetaArticleAuthor(): ?string;

    /**
     * @return string|null
     */
    public function getMetaArticlePublisher(): ?string;

    /**
     * @return string|null
     */
    public function getMetaOgSiteName(): ?string;

    /**
     * @return string|null
     */
    public function getMetaTwitterSite(): ?string;
}
