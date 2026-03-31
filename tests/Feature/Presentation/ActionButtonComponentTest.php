<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders link with permission when user has permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440500', 'name' => 'SA', 'email' => 'sa-btn@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button permission="users.list.read" href="/users" icon="heroicon-o-pencil-square" label="Edit users" />',
    );

    expect($rendered)
        ->toContain('href="/users"')
        ->toContain('aria-label="Edit users"')
        ->toContain('data-tooltip="Edit users"')
        ->toContain('cursor-pointer');
});

it('hides link when user lacks permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440501', 'name' => 'NoPerm', 'email' => 'noperm-btn@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button permission="users.list.update" href="/users/1/edit" icon="heroicon-o-pencil-square" label="Edit" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders form with confirm attributes', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440502', 'name' => 'SA', 'email' => 'sa-confirm@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button permission="users.list.delete" action="/users/1" method="DELETE" icon="heroicon-o-trash" label="Delete user" variant="danger" confirm confirm-title="Delete?" confirm-message="Are you sure?" />',
    );

    expect($rendered)
        ->toContain('data-confirm-delete')
        ->toContain('data-confirm-title="Delete?"')
        ->toContain('data-confirm-message="Are you sure?"')
        ->toContain('action="/users/1"')
        ->toContain('hover:bg-red-50')
        ->toContain('aria-label="Delete user"')
        ->toContain('data-tooltip="Delete user"')
        ->toContain('cursor-pointer');
});

it('renders nothing when neither permission nor skip-permission is set', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440503', 'name' => 'SA', 'email' => 'sa-fail@test.com']);
    $this->assignSuperAdmin($user->id);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button href="/users" icon="heroicon-o-pencil-square" label="Edit" />',
    );

    expect(trim($rendered))->toBe('');
});

it('renders unconditionally with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440504', 'name' => 'NoPerm', 'email' => 'skip-btn@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button skip-permission href="/impersonate" icon="heroicon-o-finger-print" label="Impersonate" />',
    );

    expect($rendered)
        ->toContain('href="/impersonate"')
        ->toContain('aria-label="Impersonate"')
        ->toContain('data-tooltip="Impersonate"')
        ->toContain('cursor-pointer');
});

it('renders danger variant with correct classes', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440505', 'name' => 'NoPerm', 'email' => 'danger-btn@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button skip-permission href="/delete" icon="heroicon-o-trash" label="Delete" variant="danger" />',
    );

    expect($rendered)
        ->toContain('hover:bg-red-50')
        ->toContain('hover:text-red-600');
});

it('renders default variant with indigo classes', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440506', 'name' => 'NoPerm', 'email' => 'default-btn@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button skip-permission href="/edit" icon="heroicon-o-pencil-square" label="Edit" />',
    );

    expect($rendered)
        ->toContain('hover:bg-indigo-50')
        ->toContain('hover:text-indigo-600');
});

it('renders form with skip-permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440507', 'name' => 'NoPerm', 'email' => 'skip-form@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-action-button skip-permission action="/impersonate/1" icon="heroicon-o-finger-print" label="Impersonate" />',
    );

    expect($rendered)
        ->toContain('action="/impersonate/1"')
        ->toContain('aria-label="Impersonate"')
        ->toContain('data-tooltip="Impersonate"')
        ->toContain('cursor-pointer');
});
