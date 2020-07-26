<?php

declare(strict_types=1);

namespace App\Validator\Constraints\VerificationRequestExist;

use Symfony\Component\Validator\Constraint;

class VerificationRequestExist extends Constraint
{
    public $message = "Verification request is already exist";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}