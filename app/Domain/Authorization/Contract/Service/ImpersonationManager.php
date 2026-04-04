<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

interface ImpersonationManager
{
    public function start(string $impersonatorId, string $targetUserId): void;

    public function stop(): void;

    public function isActive(): bool;

    public function realUserId(): ?string;

    public function impersonatedUserId(): ?string;
}
