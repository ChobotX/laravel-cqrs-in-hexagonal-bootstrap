<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

#[RequiresPermission('files.storage.upload')]
final readonly class StoreFileCommand implements Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $uploadedBy,
        public FileUpload $upload,
    ) {}
}
