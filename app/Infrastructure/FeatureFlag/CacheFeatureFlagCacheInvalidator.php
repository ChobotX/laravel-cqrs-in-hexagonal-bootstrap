<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlag;

use App\Contract\Tenancy\TenantContext;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagCacheInvalidator;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CacheFeatureFlagCacheInvalidator implements FeatureFlagCacheInvalidator
{
    private const string CACHE_KEY_PREFIX = 'feature-flags:';

    public function __construct(
        private CacheRepository $cacheRepository,
        private TenantContext $tenantContext,
    ) {}

    public function invalidate(): void
    {
        if (! $this->tenantContext->isResolved()) {
            return;
        }

        $this->cacheRepository->forget(self::CACHE_KEY_PREFIX.$this->tenantContext->currentTenantId());
    }
}
