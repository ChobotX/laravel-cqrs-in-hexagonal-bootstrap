<?php

declare(strict_types=1);

it('creates a tenant with domain', function (): void {
    $this->artisan('tenant:create', ['name' => 'Test Corp', 'slug' => 'testcorp', '--domain' => 'testcorp'])
        ->assertSuccessful();

    $this->assertDatabaseHas('tenants', ['slug' => 'testcorp'], 'landlord');
    $this->assertDatabaseHas('tenant_domains', ['domain' => 'testcorp'], 'landlord');
});

it('creates a tenant without domain', function (): void {
    $this->artisan('tenant:create', ['name' => 'No Domain Corp', 'slug' => 'nodomain'])
        ->assertSuccessful();

    $this->assertDatabaseHas('tenants', ['slug' => 'nodomain'], 'landlord');
});
