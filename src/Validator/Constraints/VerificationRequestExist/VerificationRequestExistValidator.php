<?php

declare(strict_types=1);

namespace App\Validator\Constraints\VerificationRequestExist;

use App\Repository\VerificationRequestRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class VerificationRequestExistValidator extends ConstraintValidator
{
    private $security;
    private $requestRepository;

    public function __construct(Security $security, VerificationRequestRepository $requestRepository)
    {
        $this->security = $security;
        $this->requestRepository = $requestRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof VerificationRequestExist) {
            throw new UnexpectedTypeException($constraint, VerificationRequestExist::class);
        }

        $user = $this->security->getUser();

        if ($this->requestRepository->hasByUser($user)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}