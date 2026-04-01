<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Contract\UserId;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public UserName $name,
        public Email $email,
    ) {}
}
