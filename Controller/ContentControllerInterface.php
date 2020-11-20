<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content controller
 */
interface ContentControllerInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request            Request
     * @param object                                    $content            Content
     * @param bool                                      $checkAccessibility Whether to check content accessibility
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, object $content, bool $checkAccessibility = true): Response;

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb     Query builder
     * @param string                     $locale Locale
     */
    public function handleQueryBuilder(QueryBuilder $qb, string $locale): void;

    /**
     * @return string
     */
    public function getContentClass(): string;
}
