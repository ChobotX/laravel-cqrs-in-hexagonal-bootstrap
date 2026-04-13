<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;
use Illuminate\Support\Facades\DB;

it('is idempotent across consecutive seed calls', function (): void {
    $tenantDefaultEmailTemplateSeeder = app(TenantDefaultEmailTemplateSeeder::class);

    $tenantDefaultEmailTemplateSeeder->seed();

    $afterFirst = DB::connection('tenant')->table('email_templates')->count();

    $tenantDefaultEmailTemplateSeeder->seed();
    $afterSecond = DB::connection('tenant')->table('email_templates')->count();

    expect($afterSecond)->toBe($afterFirst);
});
