<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Schema\Renderer;

/**
 * Web page schema renderer
 */
interface WebPageSchemaRendererInterface
{
    /**
     * @param string         $name          Name
     * @param string|null    $description   Description
     * @param \DateTime|null $dateModified  Date modified
     * @param \DateTime|null $datePublished Date published
     * @param string|null    $url           URL
     *
     * @return string
     */
    public function renderWebPageSchema(
        string $name,
        ?string $description = null,
        ?\DateTime $dateModified = null,
        ?\DateTime $datePublished = null,
        ?string $url = null
    ): string;
}
