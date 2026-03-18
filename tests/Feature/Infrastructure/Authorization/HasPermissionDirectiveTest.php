<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('hasPermission directive returns true for super admin', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440460', 'name' => 'SA', 'email' => 'sa@dir.com']);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user);

    $rendered = Blade::render('@hasPermission("users.list.read") YES @endhasPermission');

    expect(trim($rendered))->toBe('YES');
});

it('hasPermission directive returns false for user without permission', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440461', 'name' => 'NoPerm', 'email' => 'noperm@dir.com']);

    $this->actingAs($user);

    $rendered = Blade::render('@hasPermission("users.list.read") YES @else NO @endhasPermission');

    expect(trim($rendered))->toBe('NO');
});

it('hasPermission directive returns false when not authenticated', function (): void {
    $rendered = Blade::render('@hasPermission("users.list.read") YES @else NO @endhasPermission');

    expect(trim($rendered))->toBe('NO');
});
