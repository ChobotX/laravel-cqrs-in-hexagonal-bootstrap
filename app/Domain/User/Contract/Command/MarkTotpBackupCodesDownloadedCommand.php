<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users record own TOTP backup download')]
final readonly class MarkTotpBackupCodesDownloadedCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {}
}
