<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static VerificationRequestStatus VERIFICATION_REQUESTED()
 * @method static VerificationRequestStatus APPROVED()
 * @method static VerificationRequestStatus DECLINED()
 *
 * @psalm-template T
 * @psalm-immutable
 */
class VerificationRequestStatus extends Enum
{
    const VERIFICATION_REQUESTED = 'verification_requested';
    const APPROVED = 'approved';
    const DECLINED = 'declined';

    public static function getReviewedStatuses(): array
    {
        return [self::APPROVED, self::DECLINED];
    }
}