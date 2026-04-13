<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Service\TenantAdminUserSnapshotFactory;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;

final readonly class DefaultTenantAdminUserSnapshotFactory implements TenantAdminUserSnapshotFactory
{
    public function createFromPrimitives(string $id, string $name, string $email): User
    {
        return new User(
            id: new UserId($id),
            name: new UserName($name),
            email: new Email($email),
        );
    }
}
