<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class SaveGridPresetCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $gridName,
        public string $name,
        public string $filters,
        public string $sorting,
        public string $search,
        public bool $isDefault,
        public int $position,
        public string $scope = 'personal',
        public ?string $teamId = null,
    ) {}

    public function auditEntityType(): string
    {
        return 'grid_preset';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}
