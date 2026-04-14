<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTwoFactorSettingsCommand implements Command
{
    public function __construct(
        public bool $requiredForAllUsers,
        public bool $emailOtpEnabled,
        public bool $totpEnabled,
    ) {}
}
