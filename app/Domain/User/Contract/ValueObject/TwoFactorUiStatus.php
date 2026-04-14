<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

final readonly class TwoFactorUiStatus
{
    public function __construct(
        public bool $required,
        public bool $requiresSetup,
        public bool $requiresChallenge,
        public bool $emailAllowed,
        public bool $totpAllowed,
        public bool $emailOtpActive,
    ) {}
}
