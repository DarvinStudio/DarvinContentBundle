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

use Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface;
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
     * @var \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface
     */
    private $providerConfig;

    /**
     * @param \Darvin\Utils\Callback\CallbackRunnerInterface                             $callbackRunner Callback runner
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                               $localeProvider Locale provider
     * @param \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface $providerConfig Autocomplete provider configuration
     */
    public function __construct(
        CallbackRunnerInterface $callbackRunner,
        LocaleProviderInterface $localeProvider,
        ProviderConfigInterface $providerConfig
    ) {
        $this->callbackRunner = $callbackRunner;
        $this->localeProvider = $localeProvider;
        $this->providerConfig = $providerConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function autocomplete(string $providerName, string $term): array
    {
        $term = trim($term);

        if ('' === $term) {
            throw new \InvalidArgumentException('Search term is empty.');
        }

        $provider = $this->providerConfig->getProvider($providerName);

        $data = $this->callbackRunner->runCallback(
            $provider->getService(),
            $provider->getMethod(),
            $term,
            null,
            $this->localeProvider->getCurrentLocale(),
            $provider->getOptions()
        );

        if (!is_array($data)) {
            throw new \UnexpectedValueException(
                sprintf('Autocomplete provider "%s" must return array, got "%s".', $provider->getName(), gettype($data))
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
    public function getChoiceLabels(string $providerName, array $choices): array
    {
        if (empty($choices)) {
            return [];
        }

        $provider = $this->providerConfig->getProvider($providerName);

        return $this->callbackRunner->runCallback(
            $provider->getService(),
            $provider->getMethod(),
            null,
            $choices,
            $this->localeProvider->getCurrentLocale(),
            $provider->getOptions()
        );
    }
}
