<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<string> */
#[RequiresPermission('files.storage.read')]
final readonly class GetFileContentQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
