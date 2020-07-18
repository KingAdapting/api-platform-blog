<?php

declare(strict_types=1);

namespace App\Controller\VerificationRequest\Create;

use App\Entity\VerificationRequest;
use Symfony\Component\HttpFoundation\Request;

class Command
{
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(Request $request): VerificationRequest
    {
        return $this->handler->handle($request);
    }
}