<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use DateTimeImmutable;

final readonly class UserMapper
{
    public function toDomain(UserModel $userModel): User
    {
        $passwordChangedAt = null;

        if ($userModel->password_changed_at !== null) {
            $passwordChangedAt = DateTimeImmutable::createFromInterface($userModel->password_changed_at);
        }

        return new User(
            id: new UserId($userModel->id),
            name: new UserName($userModel->name),
            email: new Email($userModel->email),
            isActivated: $userModel->password !== null,
            avatarFileId: $userModel->avatar_file_id !== null ? new FileId($userModel->avatar_file_id) : null,
            passwordChangedAt: $passwordChangedAt,
        );
    }
}
