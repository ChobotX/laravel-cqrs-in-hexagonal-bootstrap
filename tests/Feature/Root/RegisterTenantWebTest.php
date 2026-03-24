<?php

declare(strict_types=1);

it('shows registration form', function (): void {
    app()->forgetScopedInstances();

    $this->get('http://laravel-bootstrap.local/register')
        ->assertOk();
});

it('creates tenant via registration form', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'New Corp',
        'slug' => 'newcorp',
        'domain' => 'newcorp',
    ])->assertRedirect();

    $this->assertDatabaseHas('tenants', ['slug' => 'newcorp'], 'landlord');
    $this->assertDatabaseHas('tenant_domains', ['domain' => 'newcorp'], 'landlord');
});

it('validates required fields', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [])
        ->assertSessionHasErrors(['name', 'slug', 'domain']);
});

it('validates slug format', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Valid Name',
        'slug' => 'INVALID SLUG!',
        'domain' => 'valid',
    ])->assertSessionHasErrors(['slug']);
});

it('validates slug uniqueness', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Duplicate',
        'slug' => 'test',
        'domain' => 'unique-domain',
    ])->assertSessionHasErrors(['slug']);
});

it('validates domain uniqueness', function (): void {
    app()->forgetScopedInstances();

    $this->post('http://laravel-bootstrap.local/register', [
        'name' => 'Duplicate Domain',
        'slug' => 'unique-slug',
        'domain' => 'test',
    ])->assertSessionHasErrors(['domain']);
});
