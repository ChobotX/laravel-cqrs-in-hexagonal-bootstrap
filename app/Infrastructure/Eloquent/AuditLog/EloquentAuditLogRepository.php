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
        $query = $this->applyFilters(
            AuditLogModel::query(),
            $entityType,
            $entityId,
            $userId,
            $traceId,
            $from,
            $to,
        );

        return array_values(
            $query->orderByDesc('occurred_at')
                ->limit(self::MAX_RESULTS)
                ->get()
                ->map(fn (AuditLogModel $model): AuditLogEntry => $this->auditLogMapper->toDomain($model))
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
                ->map(fn (AuditLogModel $model): AuditLogEntry => $this->auditLogMapper->toDomain($model))
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
     * @param  Builder<AuditLogModel>  $query
     * @return Builder<AuditLogModel>
     */
    private function applyFilters(
        Builder $query,
        ?string $entityType,
        ?string $entityId,
        ?string $userId,
        ?string $traceId,
        ?DateTimeImmutable $from,
        ?DateTimeImmutable $to,
    ): Builder {
        if ($entityType !== null) {
            $query->where('entity_type', $entityType);
        }

        if ($entityId !== null) {
            $query->where('entity_id', $entityId);
        }

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($traceId !== null) {
            $query->where('trace_id', $traceId);
        }

        if ($from !== null) {
            $query->where('occurred_at', '>=', $from);
        }

        if ($to !== null) {
            $query->where('occurred_at', '<=', $to);
        }

        return $query;
    }
}
