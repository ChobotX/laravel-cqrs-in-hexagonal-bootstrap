<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

final readonly class TwoFactorSettings
{
    public function __construct(
        public bool $requiredForAllUsers,
        public bool $emailOtpEnabled,
        public bool $totpEnabled,
    ) {}

    public function hasAnyMethodEnabled(): bool
    {
        return $this->emailOtpEnabled || $this->totpEnabled;
    }
}
