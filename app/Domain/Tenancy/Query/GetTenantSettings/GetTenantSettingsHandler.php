<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Query\GetTenantSettings;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use App\Domain\Tenancy\TenantSettings;
use App\Domain\Tenancy\TenantSettingsRepository;

/** @implements QueryHandler<GetTenantSettingsQuery, TenantSettings> */
final readonly class GetTenantSettingsHandler implements QueryHandler
{
    public function __construct(
        private TenantSettingsRepository $tenantSettingsRepository,
    ) {}

    public function handle(Query $query): TenantSettings
    {
        $settings = $this->tenantSettingsRepository->findByTenantId($query->tenantId);

        if (! $settings instanceof TenantSettings) {
            throw new TenantNotFoundException($query->tenantId);
        }

        return $settings;
    }
}
