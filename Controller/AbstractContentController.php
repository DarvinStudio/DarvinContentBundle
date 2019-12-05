<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Content controller abstract implementation
 */
abstract class AbstractContentController implements ContentControllerInterface
{
    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @param \Twig\Environment $twig Twig
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function handleQueryBuilder(QueryBuilder $qb, string $locale): void
    {

    }

    /**
     * @param string $template Template
     * @param array  $context  Context
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render(string $template, array $context = []): Response
    {
        return new Response($this->renderView($template, $context));
    }

    /**
     * @param string $template Template
     * @param array  $context  Context
     *
     * @return string
     */
    protected function renderView(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
