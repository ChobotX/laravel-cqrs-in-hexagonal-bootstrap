<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\File\Contract\Entity\File;

/** @implements Query<File> */
#[RequiresPermission('files.storage.read')]
final readonly class GetLatestFileVersionQuery implements Query
{
    public function __construct(
        public string $namespace,
        public string $originalName,
    ) {}
}
