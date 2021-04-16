<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension\Schema;

use Darvin\ContentBundle\Schema\Renderer\WebPageSchemaRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Web page schema Twig extension
 */
class WebPageExtension extends AbstractExtension
{
    /**
     * @var \Darvin\ContentBundle\Schema\Renderer\WebPageSchemaRendererInterface
     */
    private $renderer;

    /**
     * @param \Darvin\ContentBundle\Schema\Renderer\WebPageSchemaRendererInterface $renderer Web page schema renderer
     */
    public function __construct(WebPageSchemaRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('schema_web_page', [$this->renderer, 'renderWebPageSchema'], ['is_safe' => ['html']]),
        ];
    }
}
