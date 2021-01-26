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

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\ContentBundle\Repository\ContentReferenceRepository;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Content front controller
 */
class FrontController
{
    /**
     * @var \Darvin\ContentBundle\Controller\ContentControllerRegistryInterface
     */
    private $controllerRegistry;

    /**
     * @var \Doctrine\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private $translationJoiner;

    /**
     * @param \Darvin\ContentBundle\Controller\ContentControllerRegistryInterface $controllerRegistry Content controller registry
     * @param \Doctrine\Persistence\ObjectManager                                 $om                 Object manager
     * @param \Symfony\Component\Routing\RouterInterface                          $router             Router
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface       $translationJoiner  Translation joiner
     */
    public function __construct(
        ContentControllerRegistryInterface $controllerRegistry,
        ObjectManager $om,
        RouterInterface $router,
        TranslationJoinerInterface $translationJoiner
    ) {
        $this->controllerRegistry = $controllerRegistry;
        $this->om = $om;
        $this->router = $router;
        $this->translationJoiner = $translationJoiner;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $slug    Content slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $slugLowercase = mb_strtolower($slug);

        if ($slugLowercase !== $slug) {
            $redirectUrl = $this->router->generate('darvin_content_show', array_merge($request->query->all(), [
                'slug' => $slugLowercase,
            ]));

            return new RedirectResponse($redirectUrl, 301);
        }

        $reference = $this->getContentReference($slug);

        try {
            $contentController = $this->controllerRegistry->getController($reference->getObjectClass());
        } catch (ControllerNotExistsException $ex) {
            throw new NotFoundHttpException($ex->getMessage(), $ex);
        }

        $content = $this->getContent(
            $reference->getObjectClass(),
            $reference->getObjectId(),
            $request->getLocale(),
            $contentController
        );

        if (null === $content) {
            $message = sprintf(
                'Unable to find content object "%s" by ID "%s".',
                $reference->getObjectClass(),
                $reference->getObjectId()
            );

            throw new NotFoundHttpException($message);
        }

        return $contentController->__invoke($request, $content);
    }

    /**
     * @param string                                                      $objectClass       Content object class
     * @param string                                                      $objectId          Content object ID
     * @param string                                                      $locale            Locale
     * @param \Darvin\ContentBundle\Controller\ContentControllerInterface $contentController Content controller
     *
     * @return object|null
     */
    private function getContent(string $objectClass, string $objectId, string $locale, ContentControllerInterface $contentController): ?object
    {
        $repository = $this->om->getRepository($objectClass);

        if (!$repository instanceof EntityRepository) {
            return $repository->find($objectId);
        }

        $qb = $repository->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $objectId);

        if (is_a($objectClass, TranslatableInterface::class, true)) {
            $this->translationJoiner->joinTranslation($qb, true, $locale, null, true);
        }

        $contentController->handleQueryBuilder($qb, $locale);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $slug Content slug
     *
     * @return \Darvin\ContentBundle\Entity\ContentReference
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getContentReference(string $slug): ContentReference
    {
        $reference = $this->getContentReferenceRepository()->findOneBy([
            'slug' => $slug,
        ]);

        if (null === $reference) {
            throw new NotFoundHttpException(sprintf('Unable to find content reference by slug "%s".', $slug));
        }

        return $reference;
    }

    /**
     * @return \Darvin\ContentBundle\Repository\ContentReferenceRepository
     */
    private function getContentReferenceRepository(): ContentReferenceRepository
    {
        return $this->om->getRepository(ContentReference::class);
    }
}
