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
    }

    /**
     * {@inheritDoc}
     */
    public function autocomplete(string $provider, string $term): array
    {
        if (!$this->hasProvider($provider)) {
            throw new \InvalidArgumentException(sprintf('Autocomplete provider "%s" does not exist.', $provider));
        }

        $definition = $this->providerDefinitions[$provider];

        return $this->callbackRunner->runCallback(
            $definition['service'],
            $definition['method'],
            $term,
            $this->localeProvider->getCurrentLocale(),
            ...$definition['extra_args']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function hasProvider(string $provider): bool
    {
        return isset($this->providerDefinitions[$provider]);
    }
}
