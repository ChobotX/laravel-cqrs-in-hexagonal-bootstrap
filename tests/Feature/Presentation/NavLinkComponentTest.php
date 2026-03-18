<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders nav link when user has permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440620', 'name' => 'SA', 'email' => 'nl-perm@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link permission="users.list.read" href="/users" icon="heroicon-o-users" label="Users" />',
    );

    expect($rendered)
        ->toContain('href="/users"')
        ->toContain('title="Users"')
        ->toContain('Users');
});

it('hides nav link when user lacks permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440621', 'name' => 'NP', 'email' => 'nl-noperm@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link permission="users.list.read" href="/users" icon="heroicon-o-users" label="Users" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders active state with aria-current', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440622', 'name' => 'SA', 'email' => 'nl-active@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link permission="users.list.read" href="/users" icon="heroicon-o-users" label="Users" :active="true" />',
    );

    expect($rendered)
        ->toContain('aria-current=page')
        ->toContain('bg-indigo-600');
});

it('renders inactive state without aria-current', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440623', 'name' => 'SA', 'email' => 'nl-inactive@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link permission="users.list.read" href="/users" icon="heroicon-o-users" label="Users" :active="false" />',
    );

    expect($rendered)
        ->not->toContain('aria-current=page')
        ->toContain('text-gray-300');
});

it('renders nothing without permission or skip-permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440624', 'name' => 'SA', 'email' => 'nl-fail@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link href="/users" icon="heroicon-o-users" label="Users" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440625', 'name' => 'NP', 'email' => 'nl-skip@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-nav-link skip-permission href="/dashboard" icon="heroicon-o-home" label="Home" />',
    );

    expect($rendered)
        ->toContain('href="/dashboard"')
        ->toContain('title="Home"')
        ->toContain('Home');
});
