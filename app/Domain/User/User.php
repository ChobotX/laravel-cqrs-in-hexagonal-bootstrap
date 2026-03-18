<?php

declare(strict_types=1);

namespace App\Domain\User;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public string $name,
        public Email $email,
    ) {}
}
