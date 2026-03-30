<?php

declare(strict_types=1);

it('migrates a specific tenant', function (): void {
    $this->artisan('tenant:migrate', ['--tenant' => testTenantSlug()])
        ->assertSuccessful();
});

it('migrates all tenants', function (): void {
    $this->artisan('tenant:migrate')
        ->assertSuccessful();
});
