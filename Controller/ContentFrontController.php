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

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Repository\SlugMapItemRepository;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content front controller
 */
class ContentFrontController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $slug    Content slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, string $slug): Response
    {
        $slugMapItem = $this->getSlugMapItem($slug);

        try {
            $contentController = $this->getContentControllerPool()->getController($slugMapItem->getObjectClass());
        } catch (ControllerNotExistsException $ex) {
            throw $this->createNotFoundException($ex->getMessage(), $ex);
        }

        $content = $this->getContent(
            $slugMapItem->getObjectClass(),
            $slugMapItem->getObjectId(),
            $request->getLocale(),
            $contentController
        );

        if (empty($content)) {
            $message = sprintf(
                'Unable to find content object "%s" by ID "%s".',
                $slugMapItem->getObjectClass(),
                $slugMapItem->getObjectId()
            );

            throw $this->createNotFoundException($message);
        }

        return $contentController->showAction($request, $content);
    }

    /**
     * @param string                                                      $objectClass       Content object class
     * @param string                                                      $objectId          Content object ID
     * @param string                                                      $locale            Locale
     * @param \Darvin\ContentBundle\Controller\ContentControllerInterface $contentController Content controller
     *
     * @return object
     */
    private function getContent(string $objectClass, string $objectId, string $locale, ContentControllerInterface $contentController)
    {
        $repository = $this->getDoctrine()->getRepository($objectClass);

        if (!$repository instanceof EntityRepository) {
            return $repository->find($objectId);
        }

        $qb = $repository->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $objectId);

        $translationJoiner = $this->getTranslationJoiner();

        if ($translationJoiner->isTranslatable($objectClass)) {
            $translationJoiner->joinTranslation($qb, true, $locale, null, true);
        }

        $contentController->handleQueryBuilder($qb, $locale);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $slug Content slug
     *
     * @return \Darvin\ContentBundle\Entity\SlugMapItem
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getSlugMapItem(string $slug): SlugMapItem
    {
        $slugMapItem = $this->getSlugMapItemRepository()->findOneBy([
            'slug' => $slug,
        ]);

        if (empty($slugMapItem)) {
            throw $this->createNotFoundException(sprintf('Unable to find slug map item by slug "%s".', $slug));
        }

        return $slugMapItem;
    }

    /**
     * @return \Darvin\ContentBundle\Controller\ContentControllerPoolInterface
     */
    private function getContentControllerPool(): ContentControllerPoolInterface
    {
        return $this->get('darvin_content.controller.pool');
    }

    /**
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(): SlugMapItemRepository
    {
        return $this->getDoctrine()->getRepository(SlugMapItem::class);
    }

    /**
     * @return \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private function getTranslationJoiner(): TranslationJoinerInterface
    {
        return $this->get('darvin_content.translatable.translation_joiner');
    }
}
