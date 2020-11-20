<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\CanonicalUrl;

/**
 * Canonical URL generator
 */
interface CanonicalUrlGeneratorInterface
{
    /**
     * @param string|null $route       Route
     * @param array|null  $routeParams Route parameters
     *
     * @return string|null
     */
    public function generateCanonicalUrl(?string $route = null, ?array $routeParams = null): ?string;
}
