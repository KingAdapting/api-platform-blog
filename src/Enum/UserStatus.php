<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static self ROLE_USER
 * @method static self ROLE_BLOGGER
 * @method static self ROLE_ADMIN
 */
class UserStatus extends Enum
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_BLOGGER = 'ROLE_BLOGGER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
}