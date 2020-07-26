<?php

declare(strict_types=1);

namespace App\Controller\VerificationRequest\Review;

use App\Entity\VerificationRequest;
use App\Enum\UserRole;
use App\Enum\VerificationRequestStatus;
use App\Exception\RouteNameChangedException;
use App\Exception\VerificationRequestIsAlreadyCheckedException;
use App\Service\Mailer\VerificationRequestMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class Handler
{
    private const DECLINE_ROUTE_NAME = 'api_verification_requests_decline_item';
    private const APPROVE_ROUTE_NAME = 'api_verification_requests_approve_item';

    private $verificationRequestMailer;
    private $security;

    public function __construct(VerificationRequestMailer $verificationRequestMailer, Security $security)
    {
        $this->verificationRequestMailer = $verificationRequestMailer;
        $this->security = $security;
    }

    public function handle(VerificationRequest $data, Request $request): VerificationRequest
    {
        if (in_array($data->getStatus(), VerificationRequestStatus::getReviewedStatuses())) {
            throw new VerificationRequestIsAlreadyCheckedException(
                'This verification request is already reviewed'
            );
        }

        switch ($request->attributes->get('_route')) {
            case self::DECLINE_ROUTE_NAME:
                $data->setStatus(VerificationRequestStatus::DECLINED);
                break;
            case self::APPROVE_ROUTE_NAME:
                $data->setStatus(VerificationRequestStatus::APPROVED);
                $data->getAuthor()->addRole(UserRole::ROLE_BLOGGER);
                break;
            default:
                throw new RouteNameChangedException(
                    'Route name changed, please adjust command to new route name'
                );
        }

        $this->verificationRequestMailer->sendNotificationEmail($data->getAuthor(), $data);

        return $data;
    }
}