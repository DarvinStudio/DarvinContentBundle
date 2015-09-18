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

use Darvin\ContentBundle\Entity\SlugMapItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content front controller
 */
class ContentFrontController extends Controller
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $slug    Content slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, $slug)
    {
        $slugMapItem = $this->getSlugMapItem($slug);

        $controllerPool = $this->getContentControllerPool();

        if (!$controllerPool->hasController($slugMapItem->getObjectClass())) {
            throw $this->createNotFoundException(
                sprintf('Content controller for class "%s" does not exist.', $slugMapItem->getObjectClass())
            );
        }

        $content = $this->getDoctrine()->getRepository($slugMapItem->getObjectClass())->find($slugMapItem->getObjectId());

        if (empty($content)) {
            $message = sprintf(
                'Unable to find content object "%s" by ID "%s".',
                $slugMapItem->getObjectClass(),
                $slugMapItem->getObjectId()
            );

            throw $this->createNotFoundException($message);
        }

        $contentController = $controllerPool->getController($slugMapItem->getObjectClass());

        return $contentController->showAction($request, $content);
    }

    /**
     * @param string $slug Content slug
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getSlugMapItem($slug)
    {
        $slugMapItem = $this->getSlugMapItemRepository()->findOneBy(array(
            'slug' => $slug,
        ));

        if (empty($slugMapItem)) {
            throw $this->createNotFoundException(sprintf('Unable to find slug map item by slug "%s".', $slug));
        }

        return $slugMapItem;
    }

    /**
     * @return \Darvin\ContentBundle\Controller\ContentControllerPool
     */
    private function getContentControllerPool()
    {
        return $this->get('darvin_content.controller.pool');
    }

    /**
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository()
    {
        return $this->getDoctrine()->getRepository(SlugMapItem::SLUG_MAP_ITEM_CLASS);
    }
}
