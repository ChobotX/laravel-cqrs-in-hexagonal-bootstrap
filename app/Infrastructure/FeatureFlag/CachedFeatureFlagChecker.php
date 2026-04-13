<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlag;

use App\Contract\Tenancy\TenantContext;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CachedFeatureFlagChecker implements FeatureFlagChecker
{
    private const string CACHE_KEY_PREFIX = 'feature-flags:';

    public function __construct(
        private FeatureFlagChecker $featureFlagChecker,
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
            return $this->featureFlagChecker->all();
        }

        $tenantId = $this->tenantContext->currentTenantId();

        /** @var array<string, array{enabled: bool, value: string}> $result */
        $result = $this->cacheRepository->remember(
            self::CACHE_KEY_PREFIX.$tenantId,
            $this->ttl,
            fn (): array => $this->featureFlagChecker->all(),
        );

        return $result;
    }
}
