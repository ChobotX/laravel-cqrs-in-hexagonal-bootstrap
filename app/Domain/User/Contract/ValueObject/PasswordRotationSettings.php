<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Tenant-level password rotation policy (singleton row in tenant schema).
 */
final readonly class PasswordRotationSettings
{
    public const int MIN_PASSWORD_AGE_DAYS = 1;

    public const int MAX_PASSWORD_AGE_DAYS = 3660;

    public const int MIN_HISTORY_COUNT = 1;

    public const int MAX_HISTORY_COUNT = 24;

    public const int DEFAULT_HISTORY_COUNT = 5;

    public function __construct(
        public bool $rotationEnabled,
        public ?int $maxAgeDays,
        public int $historyCount,
    ) {}

    public function normalizedHistoryCount(): int
    {
        return max(self::MIN_HISTORY_COUNT, min(self::MAX_HISTORY_COUNT, $this->historyCount));
    }
}
