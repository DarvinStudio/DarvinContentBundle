<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

use Darvin\ContentBundle\Autocomplete\AutocompleterInterface;
use Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Autocomplete controller
 */
class AutocompleteController
{
    /**
     * @var \Darvin\ContentBundle\Autocomplete\AutocompleterInterface
     */
    private $autocompleter;

    /**
     * @var \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface
     */
    private $providerConfig;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param \Darvin\ContentBundle\Autocomplete\AutocompleterInterface                  $autocompleter  Autocompleter
     * @param \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface $providerConfig Autocomplete provider configuration
     * @param bool                                                                       $debug          Is debug mode enabled
     */
    public function __construct(AutocompleterInterface $autocompleter, ProviderConfigInterface $providerConfig, bool $debug)
    {
        $this->autocompleter = $autocompleter;
        $this->providerConfig = $providerConfig;
        $this->debug = $debug;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request  Request
     * @param string                                    $provider Autocomplete provider name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(Request $request, string $provider): Response
    {
        if (!$this->debug && !$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Request is not XMLHttpRequest.');
        }

        $provider = str_replace('-', '_', $provider);

        if (!$this->providerConfig->hasProvider($provider)) {
            throw new NotFoundHttpException(sprintf('Autocomplete provider "%s" does not exist.', $provider));
        }

        return new JsonResponse([
            'results' => $this->autocompleter->autocomplete($provider, (string)$request->query->get('term', ''))
        ]);
    }
}
