<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use Symfony\Component\Mime\Email;

interface MailSenderInterface
{
    public function send(Email $email): void;
}