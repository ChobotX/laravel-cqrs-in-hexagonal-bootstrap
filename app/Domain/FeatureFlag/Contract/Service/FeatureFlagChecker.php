<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

interface FeatureFlagChecker
{
    public function isEnabled(string $key): bool;

    public function value(string $key): string;

    /**
     * @return array<string, array{enabled: bool, value: string}>
     */
    public function all(): array;
}
