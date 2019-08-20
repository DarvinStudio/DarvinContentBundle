<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Security\Voter\Sorting;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Reposition voter
 */
class RepositionVoter extends Voter
{
    public const REPOSITION = 'darvin_content_sorting_reposition';

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $requiredPermissions;

    /**
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker Authorization checker
     * @param array                                                                        $requiredPermissions  Required permissions
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, array $requiredPermissions)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->requiredPermissions = $requiredPermissions;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return self::REPOSITION === $attribute;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        foreach ($this->requiredPermissions as $permission) {
            if (!$this->authorizationChecker->isGranted($permission, $subject)) {
                return false;
            }
        }

        return true;
    }
}
