<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Entity\User;
use App\Enum\UserRole;

class UserBuilder
{
    public $email;
    public $role;
    public $password;

    public function viaEmail(string $email, string $passwordHash): self
    {
        $clone = clone $this;
        $clone->email = $email;
        $clone->password = $passwordHash;

        return $clone;
    }

    public function withRole(UserRole $role): self
    {
        $clone = clone $this;
        $clone->role = $role;

        return $clone;
    }

    public function build(): User
    {
        $user = null;

        if ($this->email) {
            $user = (new User())
                ->setEmail($this->email)
                ->setPassword($this->password)
            ;
        }

        if (!$user) {
            throw new \BadMethodCallException('Specify via method.');
        }

        if ($this->role) {
            $user->addRole($this->role->getValue());
        }

        return $user;
    }
}