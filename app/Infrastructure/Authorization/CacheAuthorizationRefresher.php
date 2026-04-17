<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Domain\Authorization\Contract\Service\AuthorizationRefresher;
use App\Domain\Tenancy\Contract\Service\TenantContext;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CacheAuthorizationRefresher implements AuthorizationRefresher
{
    public function __construct(
        private CacheRepository $cacheRepository,
        private TenantContext $tenantContext,
    ) {}

    public function refreshForUser(string $userId): void
    {
        $prefix = $this->tenantContext->isResolved() ? $this->tenantContext->currentTenantSlug() : 'default';

        $this->cacheRepository->increment(sprintf('%s:auth:version:%s', $prefix, $userId));
    }
}
