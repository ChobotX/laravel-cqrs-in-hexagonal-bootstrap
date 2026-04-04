<?php

declare(strict_types=1);

namespace App\Domain\User\Contract;

use App\Domain\File\Contract\FileId;
use App\Domain\User\Email;
use App\Domain\User\UserName;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public UserName $name,
        public Email $email,
        public ?FileId $avatarFileId = null,
    ) {}
}
