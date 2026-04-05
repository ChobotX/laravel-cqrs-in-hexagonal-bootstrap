<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

#[SkipPermissionCheck(reason: 'Avatar storage is an internal operation — authorization is handled by the calling controller')]
final readonly class StoreAvatarCommand implements Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $uploadedBy,
        public FileUpload $upload,
    ) {}
}
