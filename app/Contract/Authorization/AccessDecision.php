<?php

declare(strict_types=1);

namespace App\Contract\Authorization;

interface AccessDecision
{
    public function granted(): bool;

    public function scope(): string;
}
