<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static UserRole ROLE_USER()
 * @method static UserRole ROLE_BLOGGER()
 * @method static UserRole ROLE_ADMIN()
 *
 * @psalm-template T
 * @psalm-immutable
 */
class UserRole extends Enum
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_BLOGGER = 'ROLE_BLOGGER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
}