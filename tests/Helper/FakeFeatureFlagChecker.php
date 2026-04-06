<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;

final readonly class FakeFeatureFlagChecker implements FeatureFlagChecker
{
    /** @param  array<string, array{enabled: bool, value: string}>  $flags */
    public function __construct(
        private array $flags = [],
    ) {}

    public function isEnabled(string $key): bool
    {
        return $this->flags[$key]['enabled'] ?? false;
    }

    public function value(string $key): string
    {
        return $this->flags[$key]['value'] ?? '';
    }

    /** @return array<string, array{enabled: bool, value: string}> */
    public function all(): array
    {
        return $this->flags;
    }
}
