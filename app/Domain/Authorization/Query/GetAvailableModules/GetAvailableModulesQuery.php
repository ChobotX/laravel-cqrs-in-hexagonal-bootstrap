<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetAvailableModules;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/** @implements Query<array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>> */
#[SkipPermissionCheck(reason: 'Module listing is public configuration')]
final readonly class GetAvailableModulesQuery implements Query {}
