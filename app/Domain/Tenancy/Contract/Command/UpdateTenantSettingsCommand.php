<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;
use SplFileInfo;

/**
 * Command payload for update tenant settings in the Tenancy bounded context; dispatched through the command bus.
 */
#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTenantSettingsCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $tenantId,
        /** Human-visible label or title. */
        public string $name,
        /** Optional `logo`; null means not provided or not applicable. */
        public ?SplFileInfo $logo,
        /** Field `removeLogo` for this contract; see module docs for validation rules. */
        public bool $removeLogo,
        /**
         * IANA timezone from the settings form: null after validation means clear (browser-local display).
         */
        public ?string $displayTimezone,
    ) {}
}
