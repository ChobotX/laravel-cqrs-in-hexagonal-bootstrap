<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;

final readonly class EloquentAuditLogRepository implements AuditLogRepository
{
    private const int MAX_RESULTS = 500;

    public function __construct(
        private AuditLogMapper $auditLogMapper,
    ) {}

    /** @return list<AuditLogEntry> */
    public function findAll(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): array {
        $builder = $this->applyFilters(
            AuditLogModel::query(),
            $entityType,
            $entityId,
            $userId,
            $traceId,
            $from,
            $to,
        );

        return array_values(
            $builder->orderByDesc('occurred_at')
                ->limit(self::MAX_RESULTS)
                ->get()
                ->map(fn (AuditLogModel $auditLogModel): AuditLogEntry => $this->auditLogMapper->toDomain($auditLogModel))
                ->all(),
        );
    }

    /** @return list<AuditLogEntry> */
    public function findByTraceId(string $traceId): array
    {
        return array_values(
            AuditLogModel::where('trace_id', $traceId)
                ->orderBy('occurred_at')
                ->get()
                ->map(fn (AuditLogModel $auditLogModel): AuditLogEntry => $this->auditLogMapper->toDomain($auditLogModel))
                ->all(),
        );
    }

    public function count(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): int {
        return $this->applyFilters(
            AuditLogModel::query(),
            $entityType,
            $entityId,
            $userId,
            $traceId,
            $from,
            $to,
        )->count();
    }

    /**
     * @param  Builder<AuditLogModel>  $builder
     * @return Builder<AuditLogModel>
     */
    private function applyFilters(
        Builder $builder,
        ?string $entityType,
        ?string $entityId,
        ?string $userId,
        ?string $traceId,
        ?DateTimeImmutable $from,
        ?DateTimeImmutable $to,
    ): Builder {
        if ($entityType !== null) {
            $builder->where('entity_type', $entityType);
        }

        if ($entityId !== null) {
            $builder->where('entity_id', $entityId);
        }

        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }

        if ($traceId !== null) {
            $builder->where('trace_id', $traceId);
        }

        if ($from instanceof DateTimeImmutable) {
            $builder->where('occurred_at', '>=', $from);
        }

        if ($to instanceof DateTimeImmutable) {
            $builder->where('occurred_at', '<=', $to);
        }

        return $builder;
    }
}
