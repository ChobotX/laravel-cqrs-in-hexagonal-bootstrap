<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for list definition namespaces in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<list<string>>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionNamespacesQuery implements Query {}
