<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface AuthenticatedUser
{
    public function id(): ?string;

    public function name(): ?string;

    public function impersonatorId(): ?string;

    public function isImpersonating(): bool;
}
