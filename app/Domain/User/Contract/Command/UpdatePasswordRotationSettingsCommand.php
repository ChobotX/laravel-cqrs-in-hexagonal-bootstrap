<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdatePasswordRotationSettingsCommand implements Command
{
    public function __construct(
        public bool $rotationEnabled,
        public ?int $maxAgeDays,
        public int $historyCount,
    ) {}
}
