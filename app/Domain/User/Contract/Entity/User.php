<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Entity;

use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public UserName $name,
        public Email $email,
        public bool $isActivated = false,
        public ?FileId $avatarFileId = null,
    ) {}
}
