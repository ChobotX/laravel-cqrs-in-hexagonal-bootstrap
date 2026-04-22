<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\File\Contract\Entity\File;

/**
 * Query for get file by id in the File bounded context; dispatched through the query bus.
 *
 * @implements Query<File>
 */
#[RequiresPermission('files.storage.read')]
final readonly class GetFileByIdQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
