<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Post;
use App\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    private const CREATE = 'CREATE';
    private const EDIT = 'EDIT';
    private const DELETE = 'DELETE';

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate();
                break;
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            case self::DELETE:
                return $this->canDelete($subject, $user);
                break;
            default:
                return false;
        }
    }

    private function canCreate(): bool
    {
        return $this->authorizationChecker->isGranted(UserRole::ROLE_BLOGGER);
    }

    private function canEdit(Post $post, UserInterface $user): bool
    {
        return $post->getAuthor() === $user;
    }

    private function canDelete(Post $post, UserInterface $user): bool
    {
        return $post->getAuthor() === $user;
    }
}