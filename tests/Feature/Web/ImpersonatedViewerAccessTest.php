<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('viewer can access users list directly', function (): void {
    $this->seedSuperAdminRole();

    $viewerRole = $this->seedRoleWithPermissions(
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440810',
        'name' => 'Tillman Harvey',
        'email' => 'tillman-direct@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id);

    $this->post('/login', ['email' => 'tillman-direct@example.com', 'password' => 'password123']);

    $this->get('/users')->assertOk()->assertSee('app-users-grid', false);

    $response = $this->getJson('/internal-api/users/list');
    $response->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Tillman Harvey');
});

it('impersonated viewer can access users list', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440800',
        'name' => 'Admin',
        'email' => 'admin-realflow@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440801',
        'name' => 'Tillman Harvey',
        'email' => 'tillman-realflow@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id);

    $this->post('/login', ['email' => 'admin-realflow@example.com', 'password' => 'password123']);
    $this->post('/impersonate/'.$viewer->id);

    $this->get('/users')->assertOk()->assertSee('app-users-grid', false);

    $response = $this->getJson('/internal-api/users/list');
    $response->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Tillman Harvey');
});
