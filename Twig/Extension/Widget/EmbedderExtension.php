<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension\Widget;

use Darvin\ContentBundle\Widget\Embedder\WidgetEmbedderInterface;
use Darvin\Utils\Service\ServiceProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Widget embedder Twig extension
 */
class EmbedderExtension extends AbstractExtension
{
    /**
     * @var \Darvin\Utils\Service\ServiceProviderInterface
     */
    private $widgetEmbedderProvider;

    /**
     * @param \Darvin\Utils\Service\ServiceProviderInterface $widgetEmbedderProvider Widget embedder service provider
     */
    public function __construct(ServiceProviderInterface $widgetEmbedderProvider)
    {
        $this->widgetEmbedderProvider = $widgetEmbedderProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('content_embed_widgets', [$this->getWidgetEmbedder(), 'embed'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @return \Darvin\ContentBundle\Widget\Embedder\WidgetEmbedderInterface
     */
    private function getWidgetEmbedder(): WidgetEmbedderInterface
    {
        return $this->widgetEmbedderProvider->getService();
    }
}
