<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\ValueObject;

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use DateTimeImmutable;

/**
 * Contract-level value object for audit log entry used across AuditLog commands, queries, and events.
 */
final readonly class AuditLogEntry
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  list<array<string, mixed>>  $changes
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public AuditLogId $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $traceId,
        /** Optional user identifier when absent. */
        public ?string $userId,
        /** Optional impersonator identifier when absent. */
        public ?string $impersonatorId,
        /** Field `command` for this contract; see module docs for validation rules. */
        public string $command,
        /** Field `actionLabel` for this contract; see module docs for validation rules. */
        public string $actionLabel,
        /** Classifier string or type discriminator. */
        public ?string $entityType,
        /** Optional entity identifier when absent. */
        public ?string $entityId,
        /** Array for `payload`; see constructor PHPDoc for structural tags when present. */
        public array $payload,
        /** Array for `changes`; see constructor PHPDoc for structural tags when present. */
        public array $changes,
        /** Field `status` for this contract; see module docs for validation rules. */
        public AuditLogStatus $status,
        /** Optional `ipAddress`; null means not provided or not applicable. */
        public ?string $ipAddress,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}
}
