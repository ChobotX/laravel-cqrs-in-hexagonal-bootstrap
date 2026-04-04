<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\File\Contract\File;

/** @implements Query<list<File>> */
#[RequiresPermission('files.storage.read')]
final readonly class GetFileVersionsQuery implements Query
{
    public function __construct(
        public string $namespace,
        public string $originalName,
    ) {}
}
