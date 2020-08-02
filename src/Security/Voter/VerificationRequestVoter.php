<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\VerificationRequest;
use App\Enum\VerificationRequestStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VerificationRequestVoter extends Voter
{
    private const VIEW = 'VIEW';
    private const EDIT = 'EDIT';
    private const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof VerificationRequest;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            default:
                return false;
        }
    }

    private function canEdit(VerificationRequest $verificationRequest, UserInterface $user): bool
    {
        if (in_array($verificationRequest->getStatus(), VerificationRequestStatus::getReviewedStatuses())) {
            return false;
        }

        return $verificationRequest->getAuthor() === $user;
    }

    private function canView(VerificationRequest $verificationRequest, UserInterface $user): bool
    {
        return $verificationRequest->getAuthor() === $user;
    }

    private function canDelete(VerificationRequest $verificationRequest, UserInterface $user): bool
    {
        return !in_array($verificationRequest->getStatus(), VerificationRequestStatus::getReviewedStatuses())
            && $verificationRequest->getAuthor() === $user;
    }
}