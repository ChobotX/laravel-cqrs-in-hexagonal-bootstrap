<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for delete file in the File bounded context; dispatched through the command bus.
 */
#[RequiresPermission('files.storage.delete')]
final readonly class DeleteFileCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
