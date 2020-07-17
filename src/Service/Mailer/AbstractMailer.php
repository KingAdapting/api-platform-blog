<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\SerializerInterface;

class AbstractMailer implements MailSenderInterface
{
    protected $mailer;
    protected $logger;
    protected $serializer;
    protected $sender;

    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        string $sender
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->sender = $sender;
    }

    public function send(Email $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                sprintf(
                    'Failed to send email: %s with message: %s',
                    $this->serializer->serialize($email, 'json'),
                    $exception->getMessage()
                )
            );
        }
    }
}