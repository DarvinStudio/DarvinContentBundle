<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Schema\Factory;

use Darvin\SchemaBundle\Model\WebPage;

/**
 * Web page schema factory
 */
interface WebPageSchemaFactoryInterface
{
    /**
     * @param string         $name          Name
     * @param string|null    $description   Description
     * @param \DateTime|null $dateModified  Date modified
     * @param \DateTime|null $datePublished Date published
     * @param string|null    $url           URL
     *
     * @return \Darvin\SchemaBundle\Model\WebPage
     */
    public function createWebPageSchema(
        string $name,
        ?string $description = null,
        ?\DateTime $dateModified = null,
        ?\DateTime $datePublished = null,
        ?string $url = null
    ): WebPage;
}
