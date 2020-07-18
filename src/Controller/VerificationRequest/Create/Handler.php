<?php

declare(strict_types=1);

namespace App\Controller\VerificationRequest\Create;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\VerificationRequest;
use Symfony\Component\HttpFoundation\Request;

class Handler
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Request $request): VerificationRequest
    {
        $verificationRequest =  (new VerificationRequest())
            ->setIdentityDocumentFile($request->files->get('identityDocumentFile'))
            ->setMessage($request->request->get('message'))
        ;

        $this->validator->validate($verificationRequest, ['groups' => ['verification_request:create']]);

        return $verificationRequest;
    }
}