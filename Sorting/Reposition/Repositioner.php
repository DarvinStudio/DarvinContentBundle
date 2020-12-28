<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting\Reposition;

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\ContentBundle\Entity\Position;
use Darvin\ContentBundle\Repository\PositionRepository;
use Darvin\ContentBundle\Security\Voter\Sorting\RepositionVoter;
use Darvin\ContentBundle\Sorting\Reposition\Model\Reposition;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Repositioner
 */
class Repositioner implements RepositionerInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @var \Doctrine\Persistence\ObjectManager
     */
    private $om;

    /**
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker Authorization checker
     * @param \Darvin\Utils\ORM\EntityResolverInterface                                    $entityResolver       Entity resolver
     * @param \Doctrine\Persistence\ObjectManager                                          $om                   Object manager
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EntityResolverInterface $entityResolver,
        ObjectManager $om
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityResolver = $entityResolver;
        $this->om = $om;
    }

    /**
     * {@inheritDoc}
     */
    public function reposition(Reposition $reposition): void
    {
        if (!$this->authorizationChecker->isGranted(RepositionVoter::REPOSITION, $reposition->getClass())) {
            throw new AccessDeniedException();
        }
        if (0 === count($reposition->getIds())) {
            return;
        }
        if (null === $reposition->getSlug() && !$reposition->hasTags()) {
            throw new \InvalidArgumentException('Reposition slug or tags must be provided.');
        }

        $class = base64_decode(urldecode((string)$reposition->getClass()));

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Object class "%s" does not exist.', $class));
        }

        $contentReference = $this->om->getRepository(ContentReference::class)->findOneBy(['slug' => $reposition->getSlug()]);

        if (null === $contentReference) {
            throw new \InvalidArgumentException(sprintf('Content reference with slug "%s" does not exist.', $reposition->getSlug()));
        }

        $positions = $this->getPositionRepository()->getForRepositioner(
            $contentReference,
            $reposition->getTags(),
            [$class, $this->entityResolver->reverseResolve($class)],
            $reposition->getIds()
        );

        foreach (array_values($reposition->getIds()) as $value => $id) {
            $position = $positions[$id] ?? new Position($contentReference, $class, $id, $value, $reposition->getTags());
            $position->setValue($value);

            $this->om->persist($position);
        }

        $this->om->flush();
    }

    /**
     * @return \Darvin\ContentBundle\Repository\PositionRepository
     */
    private function getPositionRepository(): PositionRepository
    {
        return $this->om->getRepository(Position::class);
    }
}
