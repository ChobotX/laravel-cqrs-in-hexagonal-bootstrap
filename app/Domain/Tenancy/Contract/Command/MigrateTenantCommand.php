<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

#[SkipPermissionCheck('Tenant migration — CLI-only, no user context')]
#[SkipTransaction(reason: 'Runs migrations with own transaction management')]
final readonly class MigrateTenantCommand implements Command
{
    public function __construct(
        public string $slug,
    ) {}
}
