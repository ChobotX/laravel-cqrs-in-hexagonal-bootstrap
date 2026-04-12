<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\File\Contract\Entity\File;

/**
 * Query for get latest file version in the File bounded context; dispatched through the query bus.
 *
 * @implements Query<File>
 */
#[RequiresPermission('files.storage.read')]
final readonly class GetLatestFileVersionQuery implements Query
{
    public function __construct(
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Human-visible label or title. */
        public string $originalName,
    ) {}
}
