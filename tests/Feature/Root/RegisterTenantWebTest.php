<?php

declare(strict_types=1);

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;

it('shows registration form', function (): void {
    app()->forgetScopedInstances();

    $this->get('http://laravel-bootstrap.local/register')
        ->assertOk();
});

it('creates tenant with admin via registration form', function (): void {
    app()->forgetScopedInstances();
    $this->app->bind(InitializeTenantAdminHandler::class, static fn (): CommandHandler => new class implements CommandHandler
    {
        public function handle(Command $command): void {}
    });

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'New Corp',
        'slug' => 'newcorp',
        'domain' => 'newcorp',
        'admin_name' => 'Admin User',
        'admin_email' => 'admin@newcorp.com',
    ])->assertRedirect();

    $this->assertDatabaseHas('tenants', ['slug' => 'newcorp'], 'landlord');
    $this->assertDatabaseHas('tenant_domains', ['domain' => 'newcorp'], 'landlord');
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
