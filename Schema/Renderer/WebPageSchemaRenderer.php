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

use Darvin\ContentBundle\Schema\Factory\WebPageSchemaFactoryInterface;
use Darvin\SchemaBundle\Renderer\SchemaRendererInterface;

/**
 * Web page schema renderer
 */
class WebPageSchemaRenderer implements WebPageSchemaRendererInterface
{
    /**
     * @var \Darvin\SchemaBundle\Renderer\SchemaRendererInterface
     */
    private $genericSchemaRenderer;

    /**
     * @var \Darvin\ContentBundle\Schema\Factory\WebPageSchemaFactoryInterface
     */
    private $webPageSchemaFactory;

    /**
     * @param \Darvin\SchemaBundle\Renderer\SchemaRendererInterface              $genericSchemaRenderer Generic schema renderer
     * @param \Darvin\ContentBundle\Schema\Factory\WebPageSchemaFactoryInterface $webPageSchemaFactory  Web page schema factory
     */
    public function __construct(SchemaRendererInterface $genericSchemaRenderer, WebPageSchemaFactoryInterface $webPageSchemaFactory)
    {
        $this->genericSchemaRenderer = $genericSchemaRenderer;
        $this->webPageSchemaFactory = $webPageSchemaFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function renderWebPageSchema(
        string $name,
        ?string $description = null,
        ?\DateTime $dateModified = null,
        ?\DateTime $datePublished = null,
        ?string $url = null
    ): string {
        return $this->genericSchemaRenderer->render(
            $this->webPageSchemaFactory->createWebPageSchema($name, $description, $dateModified, $datePublished, $url)
        );
    }
}
