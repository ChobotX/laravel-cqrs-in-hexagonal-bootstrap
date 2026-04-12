<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;
use SplFileInfo;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTenantSettingsCommand implements Command
{
    public function __construct(
        public string $tenantId,
        public string $name,
        public ?SplFileInfo $logo,
        public bool $removeLogo,
    ) {}
}
