<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

use DateTimeImmutable;

final readonly class EmailTwoFactorChallenge
{
    public function __construct(
        public string $codeHash,
        public DateTimeImmutable $expiresAt,
        public int $attempts,
        public bool $consumed,
    ) {}

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $now > $this->expiresAt;
    }
}
