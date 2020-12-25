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

use Darvin\ConfigBundle\Configuration\AbstractConfiguration;
use Darvin\ConfigBundle\Parameter\ParameterModel;

/**
 * Content config
 */
class ContentConfig extends AbstractConfiguration implements ContentConfigInterface
{
    /**
     * @var string
     */
    private $defaultMetaOgSiteName;

    /**
     * @param string $defaultMetaOgSiteName Default "og:site_name" meta tag's value
     */
    public function __construct(string $defaultMetaOgSiteName)
    {
        $this->defaultMetaOgSiteName = $defaultMetaOgSiteName;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel(): iterable
    {
        foreach ([
            'meta_article_author'    => null,
            'meta_article_publisher' => null,
            'meta_og_site_name'      => $this->defaultMetaOgSiteName,
            'meta_twitter_site'      => null,
        ] as $name => $default) {
            yield new ParameterModel($name, ParameterModel::TYPE_STRING, $default, [
                'form' => [
                    'options' => [
                        'help' => sprintf('configuration.darvin_content.help.%s', $name),
                    ],
                ],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaArticleAuthor(): ?string
    {
        return $this->__call(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaArticlePublisher(): ?string
    {
        return $this->__call(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaOgSiteName(): ?string
    {
        return $this->__call(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaTwitterSite(): ?string
    {
        $site = $this->__call(__FUNCTION__);

        if (null !== $site) {
            $site = sprintf('@%s', ltrim($site, '@'));
        }

        return $site;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'darvin_content';
    }
}
