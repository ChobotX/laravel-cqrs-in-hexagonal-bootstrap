<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for get file content in the File bounded context; dispatched through the query bus.
 *
 * @implements Query<string>
 */
#[RequiresPermission('files.storage.read')]
final readonly class GetFileContentQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
