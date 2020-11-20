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

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Repository\SlugMapItemRepository;
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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @var \Darvin\ContentBundle\Router\SlugMapRouterInterface
     */
    private $slugMapRouter;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                $em             Entity manager
     * @param \Darvin\Utils\ORM\EntityResolverInterface           $entityResolver Entity resolver
     * @param \Darvin\ContentBundle\Router\SlugMapRouterInterface $slugMapRouter  Slug map router
     */
    public function __construct(EntityManagerInterface $em, EntityResolverInterface $entityResolver, SlugMapRouterInterface $slugMapRouter)
    {
        $this->em = $em;
        $this->entityResolver = $entityResolver;
        $this->slugMapRouter = $slugMapRouter;
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

        return $this->slugMapRouter->generateUrl(
            $this->getSlugMapItemRepository()->getOneByClassesAndId(
                array_unique([$class, $this->entityResolver->reverseResolve($class)]),
                array_values($this->em->getClassMetadata($class)->getIdentifierValues($entity))[0]
            ),
            $referenceType,
            $params
        );
    }

    /**
     * @return \Darvin\ContentBundle\Repository\SlugMapItemRepository
     */
    private function getSlugMapItemRepository(): SlugMapItemRepository
    {
        return $this->em->getRepository(SlugMapItem::class);
    }
}
