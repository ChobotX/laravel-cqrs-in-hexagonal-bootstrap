<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\ValueObject\AuditLogId;

final readonly class AuditLogMapper
{
    public function toDomain(AuditLogModel $model): AuditLogEntry
    {
        return new AuditLogEntry(
            id: new AuditLogId($model->id),
            traceId: $model->trace_id,
            userId: $model->user_id,
            impersonatorId: $model->impersonator_id,
            command: $model->command,
            actionLabel: $model->action_label,
            entityType: $model->entity_type,
            entityId: $model->entity_id,
            payload: $model->payload,
            status: AuditLogStatus::from($model->status),
            ipAddress: $model->ip_address,
            occurredAt: $model->occurred_at,
        );
    }
}
