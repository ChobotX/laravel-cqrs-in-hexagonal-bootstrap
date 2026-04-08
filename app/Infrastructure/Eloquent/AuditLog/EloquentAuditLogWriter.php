<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

final readonly class EloquentAuditLogWriter implements AuditLogWriter
{
    public function record(AuditLogEntry $entry): void
    {
        $model = new AuditLogModel;
        $model->id = $entry->id->value;
        $model->trace_id = $entry->traceId;
        $model->user_id = $entry->userId;
        $model->impersonator_id = $entry->impersonatorId;
        $model->command = $entry->command;
        $model->action_label = $entry->actionLabel;
        $model->entity_type = $entry->entityType;
        $model->entity_id = $entry->entityId;
        $model->payload = $entry->payload;
        $model->status = $entry->status->value;
        $model->ip_address = $entry->ipAddress;
        $model->occurred_at = $entry->occurredAt;
        $model->save();
    }
}
