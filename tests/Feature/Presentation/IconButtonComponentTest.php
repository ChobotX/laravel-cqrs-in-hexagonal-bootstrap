<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders icon button with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440650', 'name' => 'SA', 'email' => 'ib-skip@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-icon-button skip-permission icon="heroicon-o-x-mark" label="Remove" />',
    );

    expect($rendered)
        ->toContain('type="submit"')
        ->toContain('title="Remove"')
        ->toContain('aria-label="Remove"');
});

it('renders icon button with permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440651', 'name' => 'SA', 'email' => 'ib-perm@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-icon-button permission="users.roles.update" icon="heroicon-o-x-mark" label="Remove" />',
    );

    expect($rendered)
        ->toContain('type="submit"')
        ->toContain('title="Remove"');
});

it('hides icon button when user lacks permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440652', 'name' => 'NP', 'email' => 'ib-noperm@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-icon-button permission="users.roles.update" icon="heroicon-o-x-mark" label="Remove" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders nothing without permission or skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440653', 'name' => 'SA', 'email' => 'ib-fail@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-icon-button icon="heroicon-o-x-mark" label="Remove" />',
    );

    expect(trim($rendered))->toBe('');
});

it('uses custom class when provided', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440654', 'name' => 'SA', 'email' => 'ib-class@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-icon-button skip-permission icon="heroicon-o-x-mark" label="Remove" class="text-red-500" />',
    );

    expect($rendered)->toContain('text-red-500');
});
