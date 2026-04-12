<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

final readonly class EloquentAuditLogWriter implements AuditLogWriter
{
    public function record(AuditLogEntry $auditLogEntry): void
    {
        $auditLogModel = new AuditLogModel;
        $auditLogModel->id = $auditLogEntry->id->value;
        $auditLogModel->trace_id = $auditLogEntry->traceId;
        $auditLogModel->user_id = $auditLogEntry->userId;
        $auditLogModel->impersonator_id = $auditLogEntry->impersonatorId;
        $auditLogModel->command = $auditLogEntry->command;
        $auditLogModel->action_label = $auditLogEntry->actionLabel;
        $auditLogModel->entity_type = $auditLogEntry->entityType;
        $auditLogModel->entity_id = $auditLogEntry->entityId;
        $auditLogModel->payload = $auditLogEntry->payload;
        $auditLogModel->changes = $auditLogEntry->changes;
        $auditLogModel->status = $auditLogEntry->status->value;
        $auditLogModel->ip_address = $auditLogEntry->ipAddress;
        $auditLogModel->occurred_at = $auditLogEntry->occurredAt;
        $auditLogModel->save();
    }
}
