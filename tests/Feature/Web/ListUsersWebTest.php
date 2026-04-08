<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('renders user list page for authenticated user', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440020',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee('app-users-grid', false);
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/users')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/users']));
});

it('passes can-create attribute when user has create permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440022',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee('data-can-create="true"', false);
});

it('passes can-create false when user lacks create permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Viewer Only',
        'Can only view users',
        ['users.list.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440023',
        'name' => 'Viewer Only',
        'email' => 'viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    $this->actingAs($viewer)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee('data-can-create="false"', false);
});
