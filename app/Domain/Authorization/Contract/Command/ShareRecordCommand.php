<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Record sharing is enforced per-resource by the handler')]
final readonly class ShareRecordCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $granteeUserId,
        public string $resourceType,
        public string $resourceId,
        public string $action,
        public string $grantorUserId,
    ) {}

    public function auditEntityType(): string
    {
        return 'role';
    }

    public function auditEntityId(): string
    {
        return $this->resourceId;
    }
}
