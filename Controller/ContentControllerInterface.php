<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Content controller
 */
interface ContentControllerInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param object                                    $content Content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $content);

    /**
     * @return string
     */
    public function getContentClass();
}
