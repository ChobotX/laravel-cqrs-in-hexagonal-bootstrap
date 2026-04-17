<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;

final class FakeTenantBootstrapper implements TenantBootstrapper
{
    public ?string $bootstrappedDomain = null;

    public ?string $bootstrappedSlug = null;

    public bool $wasReset = false;

    public function bootstrapByDomain(string $domain): void
    {
        $this->bootstrappedDomain = $domain;
    }

    public function bootstrapBySlug(string $slug): void
    {
        $this->bootstrappedSlug = $slug;
    }

    public function reset(): void
    {
        $this->wasReset = true;
    }
}
