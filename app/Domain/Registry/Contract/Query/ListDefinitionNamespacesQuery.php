<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<list<string>> */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionNamespacesQuery implements Query {}
