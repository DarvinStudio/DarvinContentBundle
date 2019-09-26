<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\EventListener\SwitchLocaleSubscriber;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale controller
 *
 * @Route(path="/locale")
 */
class LocaleController extends Controller
{
    /**
     * @Route(name="app_locale_switch", path="/{locale}/switch", requirements={"locale"="%locale_pattern%"}, methods={"get"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $locale  Locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function switchAction(Request $request, $locale)
    {
        $baseUrl       = $request->getSchemeAndHttpHost().$request->getBaseUrl();
        $referer       = $request->headers->get('referer');
        $defaultLocale = $this->getParameter('locale');

        $prefix = $baseUrl.'/';

        if ($request->getLocale() !== $defaultLocale) {
            $prefix .= $request->getLocale().'/';
        }
        if (0 !== mb_strpos($referer, $prefix)) {
            return $this->redirectToRoute('darvin_page_homepage', [
                '_locale' => $locale,
            ]);
        }

        $request->getSession()->set(SwitchLocaleSubscriber::SESSION_KEY, true);

        $replacement = $baseUrl.'/';

        if ($locale !== $defaultLocale) {
            $replacement .= $locale.'/';
        }

        return $this->redirect(str_replace($prefix, $replacement, $referer));
    }
}
