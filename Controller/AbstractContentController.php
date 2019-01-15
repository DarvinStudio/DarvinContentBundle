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

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Content controller abstract implementation
 */
abstract class AbstractContentController extends AbstractController implements ContentControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleQueryBuilder(QueryBuilder $qb, $locale)
    {

    }
}
