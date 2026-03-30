<?php

declare(strict_types=1);

use Tests\Helper\TenantAwareRefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(TenantAwareRefreshDatabase::class, Tests\Helper\WithPermissions::class)->in('Feature');

/**
 * Per-worker tenant slug for parallel test isolation.
 * Uses TEST_TOKEN (set by ParaTest) to produce deterministic, unique slugs.
 */
function testTenantSlug(): string
{
    $token = getenv('TEST_TOKEN');

    return 'test-'.($token !== false ? $token : '1');
}

function testTenantDomain(): string
{
    return testTenantSlug();
}
