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

use Darvin\Utils\Callback\CallbackRunnerInterface;
use Darvin\Utils\Locale\LocaleProviderInterface;

/**
 * Autocompleter
 */
class Autocompleter implements AutocompleterInterface
{
    /**
     * @var \Darvin\Utils\Callback\CallbackRunnerInterface
     */
    private $callbackRunner;

    /**
     * @var \Darvin\Utils\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var array
     */
    private $providerDefinitions;

    /**
     * @var string[]
     */
    private $providerNames;

    /**
     * @param \Darvin\Utils\Callback\CallbackRunnerInterface $callbackRunner      Callback runner
     * @param \Darvin\Utils\Locale\LocaleProviderInterface   $localeProvider      Locale provider
     * @param array                                          $providerDefinitions Provider definitions
     */
    public function __construct(
        CallbackRunnerInterface $callbackRunner,
        LocaleProviderInterface $localeProvider,
        array $providerDefinitions
    ) {
        $this->callbackRunner = $callbackRunner;
        $this->localeProvider = $localeProvider;
        $this->providerDefinitions = $providerDefinitions;

        $this->providerNames = array_keys($providerDefinitions);
    }

    /**
     * {@inheritDoc}
     */
    public function autocomplete(string $provider, string $term): array
    {
        if (!$this->hasProvider($provider)) {
            throw new \InvalidArgumentException(sprintf('Autocomplete provider "%s" does not exist.', $provider));
        }

        $term = trim($term);

        if ('' === $term) {
            throw new \InvalidArgumentException('Search term is empty.');
        }

        $definition = $this->providerDefinitions[$provider];

        $data = $this->callbackRunner->runCallback(
            $definition['service'],
            $definition['method'],
            $term,
            $this->localeProvider->getCurrentLocale(),
            ...$definition['extra_args']
        );

        if (!is_iterable($data)) {
            throw new \UnexpectedValueException(
                sprintf('Autocomplete provider "%s" must return iterable, got "%s".', $provider, gettype($data))
            );
        }

        $results = [];

        foreach ($data as $id => $text) {
            $result = $text;

            if (!is_array($result)) {
                $result = [
                    'id'   => $id,
                    'text' => $text,
                ];
            }

            $results[] = $result;
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProvider(string $provider): bool
    {
        return isset($this->providerDefinitions[$provider]);
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderNames(): array
    {
        return $this->providerNames;
    }
}
