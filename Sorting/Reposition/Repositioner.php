<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting\Reposition;

use Darvin\ContentBundle\Entity\Position;
use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Repository\PositionRepository;
use Darvin\ContentBundle\Security\Voter\Sorting\RepositionVoter;
use Darvin\ContentBundle\Sorting\Reposition\Model\Reposition;
use Doctrine\ORM\EntityManager;
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
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker Authorization checker
     * @param \Doctrine\ORM\EntityManager                                                  $em                   Entity manager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, EntityManager $em)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->em = $em;
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

        $slug = $this->em->getRepository(SlugMapItem::class)->findOneBy(['slug' => $reposition->getSlug()]);

        if (null === $slug) {
            throw new \InvalidArgumentException(sprintf('Slug "%s" does not exist.', $reposition->getSlug()));
        }

        $positions = $this->getPositionRepository()->getForRepositioner($slug, $reposition->getTags(), $class, $reposition->getIds());

        foreach (array_values($reposition->getIds()) as $value => $id) {
            $position = $positions[$id] ?? new Position($slug, $class, $id, $value, $reposition->getTags());
            $position->setValue($value);

            $this->em->persist($position);
        }

        $this->em->flush();
    }

    /**
     * @return \Darvin\ContentBundle\Repository\PositionRepository
     */
    private function getPositionRepository(): PositionRepository
    {
        return $this->em->getRepository(Position::class);
    }
}
