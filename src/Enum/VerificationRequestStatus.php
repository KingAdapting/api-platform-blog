<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static self VERIFICATION_REQUESTED
 * @method static self APPROVED
 * @method static self DECLINED
 */
class VerificationRequestStatus extends Enum
{
    const VERIFICATION_REQUESTED = 'verification_requested';
    const APPROVED = 'approved';
    const DECLINED = 'declined';
}