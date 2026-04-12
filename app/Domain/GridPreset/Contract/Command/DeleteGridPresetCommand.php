<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class DeleteGridPresetCommand implements Command
{
    public function __construct(
        public string $id,
        public string $userId,
    ) {}
}
