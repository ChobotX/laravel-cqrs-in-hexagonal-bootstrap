<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;

final class FakeFeatureFlagOverrideRepository implements FeatureFlagOverrideRepository
{
    /** @var list<FeatureFlagOverride> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param  array<string, FeatureFlagOverride>  $overrides */
    public function __construct(
        private array $overrides = [],
    ) {}

    /** @return list<FeatureFlagOverride> */
    public function findAll(): array
    {
        return array_values($this->overrides);
    }

    public function findByKey(FlagKey $flagKey): ?FeatureFlagOverride
    {
        return $this->overrides[$flagKey->value] ?? null;
    }

    public function save(FeatureFlagOverride $featureFlagOverride): void
    {
        $this->overrides[$featureFlagOverride->key] = $featureFlagOverride;
        $this->saved[] = $featureFlagOverride;
    }

    public function delete(FlagKey $flagKey): void
    {
        unset($this->overrides[$flagKey->value]);
        $this->deleted[] = $flagKey->value;
    }
}
