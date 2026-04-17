<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Service;

use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;

final readonly class DefaultFeatureFlagChecker implements FeatureFlagChecker
{
    public function __construct(
        private FeatureFlagDefinitionProvider $featureFlagDefinitionProvider,
        private FeatureFlagOverrideRepository $featureFlagOverrideRepository,
    ) {}

    public function isEnabled(string $key): bool
    {
        return $this->all()[$key]['enabled'] ?? false;
    }

    public function value(string $key): string
    {
        return $this->all()[$key]['value'] ?? '';
    }

    /**
     * @return array<string, array{enabled: bool, value: string}>
     */
    public function all(): array
    {
        $overrides = $this->featureFlagOverrideRepository->findAll();

        $overrideMap = [];
        foreach ($overrides as $override) {
            $overrideMap[$override->key] = $override;
        }

        $result = [];
        foreach ($this->featureFlagDefinitionProvider->all() as $flagDefinition) {
            $key = $flagDefinition->key->value;
            $override = $overrideMap[$key] ?? null;
            $result[$key] = [
                'enabled' => $override->enabled ?? $flagDefinition->defaultEnabled,
                'value' => $override->value ?? $flagDefinition->default,
            ];
        }

        return $result;
    }
}
