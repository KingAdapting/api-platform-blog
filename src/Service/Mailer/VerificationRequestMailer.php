<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use App\Entity\User;
use App\Entity\VerificationRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class VerificationRequestMailer extends AbstractMailer
{
    private $sender;

    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        string $sender
    ) {
        parent::__construct($mailer, $logger, $serializer);
        $this->sender = $sender;
    }

    public function sendNotificationEmail(UserInterface $user, VerificationRequest $request): void
    {
        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($user->getEmail())
            ->subject('Account verification')
            ->htmlTemplate('email/account_verification.html.twig')
            ->context([
                'user' => $user,
                'request' => $request
            ])
        ;

        $this->send($email);
    }
}