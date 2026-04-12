<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

/**
 * Command payload for store avatar in the File bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Avatar storage is an internal operation — authorization is handled by the calling controller')]
final readonly class StoreAvatarCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `uploadedBy` for this contract; see module docs for validation rules. */
        public string $uploadedBy,
        /** Field `upload` for this contract; see module docs for validation rules. */
        public FileUpload $upload,
    ) {}
}
