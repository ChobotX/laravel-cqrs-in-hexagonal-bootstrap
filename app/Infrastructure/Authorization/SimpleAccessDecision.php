<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Contract\Authorization\AccessDecision;

final readonly class SimpleAccessDecision implements AccessDecision
{
    public function __construct(
        private bool $granted,
        private string $scope,
    ) {}

    public function granted(): bool
    {
        return $this->granted;
    }

    public function scope(): string
    {
        return $this->scope;
    }
}
