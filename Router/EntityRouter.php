<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Router;

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\ContentBundle\Repository\ContentReferenceRepository;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Entity router
 */
class EntityRouter implements EntityRouterInterface
{
    /**
     * @var \Darvin\ContentBundle\Router\ContentReferenceRouterInterface
     */
    private $contentReferenceRouter;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @param \Darvin\ContentBundle\Router\ContentReferenceRouterInterface $contentReferenceRouter Content reference router
     * @param \Doctrine\ORM\EntityManagerInterface                         $em                     Entity manager
     * @param \Darvin\Utils\ORM\EntityResolverInterface                    $entityResolver         Entity resolver
     */
    public function __construct(
        ContentReferenceRouterInterface $contentReferenceRouter,
        EntityManagerInterface $em,
        EntityResolverInterface $entityResolver
    ) {
        $this->contentReferenceRouter = $contentReferenceRouter;
        $this->em = $em;
        $this->entityResolver = $entityResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function generateUrl(?object $entity, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, array $params = []): ?string
    {
        if (null === $entity) {
            return null;
        }

        $class = ClassUtils::getClass($entity);

        return $this->contentReferenceRouter->generateUrl(
            $this->getContentReferenceRepository()->getOneByClassesAndId(
                array_unique([$class, $this->entityResolver->reverseResolve($class)]),
                array_values($this->em->getClassMetadata($class)->getIdentifierValues($entity))[0]
            ),
            $referenceType,
            $params
        );
    }

    /**
     * @return \Darvin\ContentBundle\Repository\ContentReferenceRepository
     */
    private function getContentReferenceRepository(): ContentReferenceRepository
    {
        return $this->em->getRepository(ContentReference::class);
    }
}
