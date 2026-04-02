<?php

declare(strict_types=1);

namespace App\Domain\File\Query\GetFileById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\File\File;

/** @implements Query<File> */
#[RequiresPermission('files.storage.read')]
final readonly class GetFileByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
