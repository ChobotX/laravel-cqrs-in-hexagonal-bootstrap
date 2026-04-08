<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('feature_flags.management.update')]
final readonly class ResetFeatureFlagCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $key,
    ) {}

    public function auditEntityType(): string
    {
        return 'feature_flag';
    }

    public function auditEntityId(): string
    {
        return $this->key;
    }
}
