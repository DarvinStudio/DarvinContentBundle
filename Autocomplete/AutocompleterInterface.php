<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Autocomplete;

/**
 * Autocompleter
 */
interface AutocompleterInterface
{
    /**
     * @param string $provider Autocomplete provider name
     * @param string $term     Term
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function autocomplete(string $provider, string $term): array;

    /**
     * @param string $provider Autocomplete provider name
     *
     * @return bool
     */
    public function hasProvider(string $provider): bool;
}
