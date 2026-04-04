<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders breadcrumb with multiple items', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'Users', 'href' => '/users'],
            ['label' => 'Create'],
        ]],
    );

    expect($rendered)
        ->toContain('href="/dashboard"')
        ->toContain('Dashboard')
        ->toContain('href="/users"')
        ->toContain('Users')
        ->toContain('Create')
        ->toContain('aria-current="page"');
});

it('does not link last item', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'Current Page'],
        ]],
    );

    expect($rendered)
        ->toContain('aria-current="page"')
        ->toContain('Current Page')
        ->not->toContain('href="/current');
});

it('renders nothing with single item', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => [
            ['label' => 'Dashboard'],
        ]],
    );

    expect(trim($rendered))->toBe('');
});

it('renders nothing with empty items', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => []],
    );

    expect(trim($rendered))->toBe('');
});

it('has aria-label for accessibility', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'Page'],
        ]],
    );

    expect($rendered)->toContain('aria-label=');
});

it('renders separator between items', function (): void {
    $rendered = Blade::render(
        '<x-breadcrumb :items="$items" />',
        ['items' => [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'Users', 'href' => '/users'],
            ['label' => 'Create'],
        ]],
    );

    expect($rendered)->toContain('aria-hidden="true"');
});
