<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use Illuminate\Support\Facades\DB;

it('shows registration form', function (): void {
    app()->forgetScopedInstances();

    $this->get('http://laravel-bootstrap.local/register')
        ->assertOk();
});

it('creates tenant with admin via registration form', function (): void {
    app()->forgetScopedInstances();

    // Random slug avoids collisions; drop orphan schema so migrate always runs on a clean slate.
    $unique = bin2hex(random_bytes(4));
    $slug = 'regtn'.$unique;
    $domain = 'regtn'.$unique;

    /** @var string $schemaPrefix */
    $schemaPrefix = config('tenancy.schema_prefix');
    $schemaName = $schemaPrefix.$slug;
    DB::connection('landlord')->statement(sprintf('DROP SCHEMA IF EXISTS "%s" CASCADE', $schemaName));

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'New Corp',
        'slug' => $slug,
        'domain' => $domain,
        'admin_name' => 'Admin User',
        'admin_email' => 'admin@newcorp.com',
    ])->assertRedirect();

    $this->assertDatabaseHas('tenants', ['slug' => $slug], 'landlord');
    $this->assertDatabaseHas('tenant_domains', ['domain' => $domain], 'landlord');

    app(TenantBootstrapper::class)->bootstrapBySlug($slug);

    $this->assertDatabaseHas('email_templates', ['type' => 'user_invite', 'locale' => 'en'], 'tenant');
    $this->assertDatabaseHas('roles', ['name' => 'Super Admin', 'is_system' => true], 'tenant');
    $this->assertDatabaseHas('users', [
        'name' => 'Admin User',
        'email' => 'admin@newcorp.com',
        'password' => null,
    ], 'tenant');

    app(TenantBootstrapper::class)->reset();
});

it('validates required fields', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [])
        ->assertSessionHasErrors(['name', 'slug', 'domain', 'admin_name', 'admin_email']);
});

it('validates slug format', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Valid Name',
        'slug' => 'INVALID SLUG!',
        'domain' => 'valid',
        'admin_name' => 'Admin',
        'admin_email' => 'admin@test.com',
    ])->assertSessionHasErrors(['slug']);
});

it('validates slug uniqueness', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Duplicate',
        'slug' => testTenantSlug(),
        'domain' => 'unique-domain',
        'admin_name' => 'Admin',
        'admin_email' => 'admin@test.com',
    ])->assertSessionHasErrors(['slug']);
});

it('validates domain uniqueness', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Duplicate Domain',
        'slug' => 'unique-slug',
        'domain' => testTenantDomain(),
        'admin_name' => 'Admin',
        'admin_email' => 'admin@test.com',
    ])->assertSessionHasErrors(['domain']);
});

it('validates admin email format', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Test',
        'slug' => 'test-email',
        'domain' => 'test-email',
        'admin_name' => 'Admin',
        'admin_email' => 'not-an-email',
    ])->assertSessionHasErrors(['admin_email']);
});
