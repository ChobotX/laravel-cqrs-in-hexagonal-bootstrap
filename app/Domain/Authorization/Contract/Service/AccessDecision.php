<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

interface AccessDecision
{
    public function granted(): bool;

    public function scope(): string;
}
