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
 *
 * @method string|null getMetaArticleAuthor()
 * @method string|null getMetaArticlePublisher()
 * @method string|null getMetaOgSiteName()
 * @method string|null getMetaTwitterSite()
 */
class ContentConfig extends AbstractConfiguration
{
    /**
     * {@inheritDoc}
     */
    public function getModel(): iterable
    {
        foreach ([
            'meta_article_author',
            'meta_article_publisher',
            'meta_og_site_name',
            'meta_twitter_site',
        ] as $name) {
            yield new ParameterModel($name, ParameterModel::TYPE_STRING, null, [
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
    public function getName(): string
    {
        return 'darvin_content';
    }
}
