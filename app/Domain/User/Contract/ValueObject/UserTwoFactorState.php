<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

use DateTimeImmutable;

final readonly class UserTwoFactorState
{
    /**
     * @param  list<string>|null  $totpRecoveryCodeHashes
     */
    public function __construct(
        public bool $emailEnabled,
        public ?DateTimeImmutable $emailConfirmedAt,
        public ?string $totpSecret,
        public ?DateTimeImmutable $totpConfirmedAt,
        public ?array $totpRecoveryCodeHashes = null,
    ) {}

    public function hasConfirmedMethod(): bool
    {
        return ($this->emailEnabled && $this->emailConfirmedAt instanceof DateTimeImmutable)
            || ($this->totpSecret !== null && $this->totpConfirmedAt instanceof DateTimeImmutable);
    }
}
