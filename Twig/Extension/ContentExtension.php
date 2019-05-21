<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Twig\Extension;

use Darvin\ContentBundle\Util\ContentUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Content Twig extension
 */
class ContentExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_empty', [ContentUtil::class, 'isEmpty']),
        ];
    }
}
