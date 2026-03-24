<?php

declare(strict_types=1);

it('returns 404 for tenant-scoped web routes on root domain', function (): void {
    $this->get('http://laravel-bootstrap.local/login')
        ->assertNotFound();
});

it('returns 404 for tenant-scoped API routes on root domain', function (): void {
    $this->getJson('http://laravel-bootstrap.local/api/users')
        ->assertNotFound();
});

it('allows root landing page without tenant', function (): void {
    $this->get('http://laravel-bootstrap.local/')
        ->assertOk();
});

it('allows root registration page without tenant', function (): void {
    $this->get('http://laravel-bootstrap.local/register')
        ->assertOk();
});

it('resolves tenant and serves login page', function (): void {
    $this->get('http://test.laravel-bootstrap.local/login')
        ->assertOk();
});
