<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders form button with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440630', 'name' => 'SA', 'email' => 'tb-skip@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-topbar-button skip-permission action="/logout" icon="heroicon-o-arrow-right-on-rectangle" label="Logout" />',
    );

    expect($rendered)
        ->toContain('action="/logout"')
        ->toContain('data-tooltip="Logout"')
        ->toContain('aria-label="Logout"')
        ->toContain('text-gray-500');
});

it('renders amber variant', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440631', 'name' => 'SA', 'email' => 'tb-amber@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-topbar-button skip-permission action="/stop-impersonation" icon="heroicon-o-arrow-uturn-left" label="Stop" variant="amber" />',
    );

    expect($rendered)
        ->toContain('text-amber-600')
        ->toContain('hover:text-amber-800');
});

it('renders nothing without permission or skip-permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440632', 'name' => 'SA', 'email' => 'tb-fail@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-topbar-button action="/logout" icon="heroicon-o-arrow-right-on-rectangle" label="Logout" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders when user has permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440633', 'name' => 'SA', 'email' => 'tb-perm@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-topbar-button permission="users.list.read" action="/some-action" icon="heroicon-o-bell" label="Notify" />',
    );

    expect($rendered)
        ->toContain('action="/some-action"')
        ->toContain('data-tooltip="Notify"')
        ->toContain('aria-label="Notify"');
});

it('hides when user lacks permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440634', 'name' => 'NP', 'email' => 'tb-noperm@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-topbar-button permission="users.list.read" action="/some-action" icon="heroicon-o-bell" label="Notify" />',
    );

    expect(trim($rendered))->toBe('');
});
