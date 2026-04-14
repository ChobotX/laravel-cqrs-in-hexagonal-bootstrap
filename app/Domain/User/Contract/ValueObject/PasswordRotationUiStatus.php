<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

use DateTimeImmutable;

/** Outcome for web UI / login flash when policy enabled. */
final readonly class PasswordRotationUiStatus
{
    public const string OK = 'ok';

    public const string WARNING = 'warning';

    public const string EXPIRED = 'expired';

    public function __construct(
        public string $value,
        public ?DateTimeImmutable $expiresAt = null,
    ) {}
}
