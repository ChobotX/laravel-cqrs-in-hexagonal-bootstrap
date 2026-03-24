<?php

declare(strict_types=1);

use Tests\Helper\TenantAwareRefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(TenantAwareRefreshDatabase::class, Tests\Helper\WithPermissions::class)->in('Feature');
