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
     * @param \Darvin\Utils\Homepage\HomepageRouterInterface $homepageRouter Homepage router
     * @param string                                         $defaultLocale  Default locale
     */
    public function __construct(HomepageRouterInterface $homepageRouter, string $defaultLocale)
    {
        $this->homepageRouter = $homepageRouter;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $locale  Locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, string $locale): Response
    {
        $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();
        $referer = $request->headers->get('referer', '');

        $currentPrefix = $targetPrefix = sprintf('%s/', $baseUrl);

        if ($request->getLocale() !== $this->defaultLocale) {
            $currentPrefix .= sprintf('%s/', $request->getLocale());
        }
        if ($locale !== $this->defaultLocale) {
            $targetPrefix .= sprintf('%s/', $locale);
        }
        if (0 !== mb_strpos($referer, $currentPrefix)) {
            $url = $this->homepageRouter->generate(UrlGeneratorInterface::ABSOLUTE_PATH, [
                '_locale' => $locale,
            ]);

            if (null === $url) {
                $url = $targetPrefix;
            }

            return new RedirectResponse($url);
        }

        $request->getSession()->set(SwitchSubscriber::SESSION_KEY, true);

        return new RedirectResponse(str_replace($currentPrefix, $targetPrefix, $referer));
    }
}
