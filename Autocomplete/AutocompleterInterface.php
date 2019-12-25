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
     * @param string $providerName Autocomplete provider name
     * @param string $term         Search term
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function autocomplete(string $providerName, string $term): array;

    /**
     * @param string $providerName Autocomplete provider name
     * @param array  $choices      Choices
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getChoiceLabels(string $providerName, array $choices): array;
}
