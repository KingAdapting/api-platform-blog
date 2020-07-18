<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractMailer
{
    protected $mailer;
    protected $logger;
    protected $serializer;

    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    protected function send(Email $email): void
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