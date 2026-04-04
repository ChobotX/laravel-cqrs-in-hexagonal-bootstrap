<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Command\DeleteFile;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('files.storage.delete')]
final readonly class DeleteFileCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
