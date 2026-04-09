<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\ValueObject\AuditLogId;

final readonly class AuditLogMapper
{
    public function toDomain(AuditLogModel $auditLogModel): AuditLogEntry
    {
        return new AuditLogEntry(
            id: new AuditLogId($auditLogModel->id),
            traceId: $auditLogModel->trace_id,
            userId: $auditLogModel->user_id,
            impersonatorId: $auditLogModel->impersonator_id,
            command: $auditLogModel->command,
            actionLabel: $auditLogModel->action_label,
            entityType: $auditLogModel->entity_type,
            entityId: $auditLogModel->entity_id,
            payload: $auditLogModel->payload,
            status: AuditLogStatus::from($auditLogModel->status),
            ipAddress: $auditLogModel->ip_address,
            occurredAt: $auditLogModel->occurred_at,
        );
    }
}
