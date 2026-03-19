<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Application\Organization\TenantAgnostic;

#[TenantAgnostic(reason: 'Users are cross-tenant — a user belongs to multiple organizations through membership')]
final readonly class User
{
    public function __construct(
        public UserId $id,
        public string $name,
        public Email $email,
    ) {}
}
