<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller\Locale;

use Darvin\ContentBundle\EventListener\Locale\SwitchSubscriber;
use Darvin\Utils\Homepage\HomepageRouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Switch locale controller
 */
class SwitchController
{
    /**
     * @var \Darvin\Utils\Homepage\HomepageRouterInterface
     */
    private $homepageRouter;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param \Darvin\Utils\Homepage\HomepageRouterInterface $homepageRouter Homepage router
     * @param string                                         $defaultLocale  Default locale
     * @param string[]                                       $locales        Locales
     */
    public function __construct(HomepageRouterInterface $homepageRouter, string $defaultLocale, array $locales)
    {
        $this->homepageRouter = $homepageRouter;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $targetLocale = $request->request->get('locale');

        if (!in_array($targetLocale, $this->locales)) {
            $targetLocale = $request->getLocale();
        }

        $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();
        $referer = $request->headers->get('referer', '');

        $currentPrefix = $targetPrefix = sprintf('%s/', $baseUrl);

        if ($request->getLocale() !== $this->defaultLocale) {
            $currentPrefix .= sprintf('%s/', $request->getLocale());
        }
        if ($targetLocale !== $this->defaultLocale) {
            $targetPrefix .= sprintf('%s/', $targetLocale);
        }
        if (0 === mb_strpos($referer, $currentPrefix)) {
            $request->getSession()->set(SwitchSubscriber::SESSION_KEY, true);

            return new RedirectResponse($targetPrefix.mb_substr($referer, mb_strlen($currentPrefix)));
        }

        $url = $this->homepageRouter->generate(UrlGeneratorInterface::ABSOLUTE_PATH, [
            '_locale' => $targetLocale,
        ]);

        if (null === $url) {
            $url = $targetPrefix;
        }

        return new RedirectResponse($url);
    }
}
