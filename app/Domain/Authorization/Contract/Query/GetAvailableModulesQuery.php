<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Query for get available modules in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>>
 */
#[SkipPermissionCheck(reason: 'Module listing is public configuration')]
final readonly class GetAvailableModulesQuery implements Query {}
