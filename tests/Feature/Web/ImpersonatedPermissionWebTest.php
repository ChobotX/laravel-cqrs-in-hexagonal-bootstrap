<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

// -- Roles page: impersonated viewer can see and perform edit/delete --

it('impersonated viewer sees view button on roles list', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440750',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm-view@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440751',
        'name' => 'Tillman Harvey',
        'email' => 'tillman-view@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    $response = $this->actingAs($admin)->get('/roles');

    $content = $response->getContent();
    expect($content)->toContain('aria-label="'.__('messages.roles.view_action').' Viewer"');
});

it('impersonated viewer can access role detail page', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440752',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm-show@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440753',
        'name' => 'Tillman Harvey',
        'email' => 'tillman-show@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    $this->actingAs($admin)
        ->get('/roles/'.$viewerRole->id)
        ->assertOk();
});

it('impersonated viewer sees edit button on roles list', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440700',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440701',
        'name' => 'Tillman Harvey',
        'email' => 'tillman@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    $response = $this->actingAs($admin)->get('/roles');

    $content = $response->getContent();
    // RED: viewer should NOT see edit button, but currently does
    expect($content)->not->toContain('aria-label="'.__('messages.roles.edit_action').' Viewer"');
});

it('impersonated viewer can access role edit form', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440702',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm2@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440703',
        'name' => 'Tillman Harvey',
        'email' => 'tillman2@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    // RED: viewer should get 403, but currently gets 200
    $this->actingAs($admin)
        ->get('/roles/'.$viewerRole->id.'/edit')
        ->assertStatus(403);
});

it('impersonated viewer can submit role edit form', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440704',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm3@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440705',
        'name' => 'Tillman Harvey',
        'email' => 'tillman3@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    // RED: viewer should get 403, but currently edit succeeds
    $this->actingAs($admin)
        ->put('/roles/'.$viewerRole->id, [
            'name' => 'Hacked Role Name',
            'description' => 'Hacked',
            'permissions' => [],
        ])
        ->assertStatus(403);

    // Verify role name was NOT changed
    $this->assertDatabaseHas('roles', [
        'id' => $viewerRole->id,
        'name' => 'Viewer',
    ]);
});

// -- Users page: impersonated viewer can see and perform edit/delete --

it('impersonated viewer sees edit button on users list', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440706',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm4@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440707',
        'name' => 'Tillman Harvey',
        'email' => 'tillman4@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    $response = $this->actingAs($admin)->get('/users');
    $response->assertOk();

    $content = $response->getContent();

    // Viewer should NOT see edit/delete/create buttons
    expect($content)->not->toContain('aria-label="'.__('messages.users.edit_action').' Tillman Harvey"');
});

it('impersonated viewer can submit user edit form', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440708',
        'name' => 'Admin Impersonator',
        'email' => 'admin-impperm5@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Viewer',
        'Read-only access',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440709',
        'name' => 'Tillman Harvey',
        'email' => 'tillman5@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id, '00000000-0000-0000-0000-000000000001');

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440710',
        'name' => 'Target User',
        'email' => 'target-impperm@example.com',
    ]);

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    // RED: viewer should get 403, but currently edit succeeds
    $this->actingAs($admin)
        ->put('/users/'.$target->id, [
            'name' => 'Hacked Name',
            'email' => 'target-impperm@example.com',
        ])
        ->assertStatus(403);

    $this->assertDatabaseHas('users', [
        'id' => $target->id,
        'name' => 'Target User',
    ]);
});
