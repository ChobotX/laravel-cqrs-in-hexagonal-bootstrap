<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\ValueObject;

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use DateTimeImmutable;

final readonly class AuditLogEntry
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public AuditLogId $id,
        public string $traceId,
        public ?string $userId,
        public ?string $impersonatorId,
        public string $command,
        public string $actionLabel,
        public ?string $entityType,
        public ?string $entityId,
        public array $payload,
        public AuditLogStatus $status,
        public ?string $ipAddress,
        public DateTimeImmutable $occurredAt,
    ) {}
}
