<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlag;

use App\Contract\Tenancy\TenantContext;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CachedFeatureFlagChecker implements FeatureFlagChecker
{
    private const string CACHE_KEY_PREFIX = 'feature-flags:';

    public function __construct(
        private FeatureFlagDefinitionProvider $featureFlagDefinitionProvider,
        private FeatureFlagOverrideRepository $featureFlagOverrideRepository,
        private CacheRepository $cacheRepository,
        private TenantContext $tenantContext,
        private int $ttl,
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
        if (! $this->tenantContext->isResolved()) {
            return $this->resolveDefaults();
        }

        $tenantId = $this->tenantContext->currentTenantId();

        /** @var array<string, array{enabled: bool, value: string}> $result */
        $result = $this->cacheRepository->remember(
            self::CACHE_KEY_PREFIX.$tenantId,
            $this->ttl,
            fn (): array => $this->resolve(),
        );

        return $result;
    }

    /**
     * @return array<string, array{enabled: bool, value: string}>
     */
    private function resolve(): array
    {
        $definitions = $this->featureFlagDefinitionProvider->all();
        $overrides = $this->featureFlagOverrideRepository->findAll();

        $overrideMap = [];
        foreach ($overrides as $override) {
            $overrideMap[$override->key] = $override;
        }

        $result = [];
        foreach ($definitions as $definition) {
            $key = $definition->key->value;
            $override = $overrideMap[$key] ?? null;
            $result[$key] = [
                'enabled' => $override->enabled ?? $definition->defaultEnabled,
                'value' => $override->value ?? $definition->default,
            ];
        }

        return $result;
    }

    /**
     * @return array<string, array{enabled: bool, value: string}>
     */
    private function resolveDefaults(): array
    {
        $result = [];

        foreach ($this->featureFlagDefinitionProvider->all() as $flagDefinition) {
            $result[$flagDefinition->key->value] = [
                'enabled' => $flagDefinition->defaultEnabled,
                'value' => $flagDefinition->default,
            ];
        }

        return $result;
    }
}
