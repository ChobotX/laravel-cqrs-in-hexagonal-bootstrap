<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders link when user has permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440610', 'name' => 'SA', 'email' => 'pb-link@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button permission="users.list.create" href="/users/create" label="Create User" />',
    );

    expect($rendered)
        ->toContain('href="/users/create"')
        ->toContain('title="Create User"')
        ->toContain('aria-label="Create User"')
        ->toContain('Create User');
});

it('hides link when user lacks permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440611', 'name' => 'NP', 'email' => 'pb-nolink@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button permission="users.list.create" href="/users/create" label="Create User" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders submit button when no href', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440612', 'name' => 'SA', 'email' => 'pb-submit@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button permission="users.roles.update" label="Add Role" />',
    );

    expect($rendered)
        ->toContain('type="submit"')
        ->toContain('title="Add Role"')
        ->toContain('aria-label="Add Role"')
        ->toContain('Add Role');
});

it('renders nothing without permission or skip-permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440613', 'name' => 'SA', 'email' => 'pb-fail@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button href="/users/create" label="Create User" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440614', 'name' => 'NP', 'email' => 'pb-skip@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button skip-permission label="Add Override" />',
    );

    expect($rendered)
        ->toContain('type="submit"')
        ->toContain('title="Add Override"')
        ->toContain('Add Override');
});

it('renders link with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440615', 'name' => 'NP', 'email' => 'pb-skiplink@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button skip-permission href="/somewhere" label="Go" />',
    );

    expect($rendered)
        ->toContain('href="/somewhere"')
        ->toContain('title="Go"');
});

it('renders secondary variant', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440616', 'name' => 'NP', 'email' => 'pb-secondary@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button skip-permission variant="secondary" href="/back" label="Cancel" />',
    );

    expect($rendered)
        ->toContain('border-gray-300')
        ->toContain('text-gray-700')
        ->toContain('Cancel');
});

it('renders amber variant with action', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440617', 'name' => 'NP', 'email' => 'pb-amber@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button skip-permission variant="amber" action="/stop" label="Stop" />',
    );

    expect($rendered)
        ->toContain('bg-amber-500')
        ->toContain('action="/stop"')
        ->toContain('Stop');
});

it('renders login variant', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440618', 'name' => 'NP', 'email' => 'pb-login@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button skip-permission variant="login" label="Sign In" />',
    );

    expect($rendered)
        ->toContain('w-full')
        ->toContain('focus:ring-2')
        ->toContain('Sign In');
});

it('renders action form with permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440619', 'name' => 'SA', 'email' => 'pb-action-perm@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-primary-button permission="users.list.delete" action="/delete" method="DELETE" label="Delete" />',
    );

    expect($rendered)
        ->toContain('action="/delete"')
        ->toContain('DELETE')
        ->toContain('Delete');
});
