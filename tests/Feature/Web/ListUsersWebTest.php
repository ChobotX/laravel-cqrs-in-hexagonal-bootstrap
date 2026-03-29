<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows users table for authenticated user', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440020',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440021',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->actingAs($user)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee('John Doe')
        ->assertSee('john@example.com');
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/users')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/users']));
});

it('shows create user button', function (): void {
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
        ->assertSee(__('messages.users.create_action'));
});

it('shows role badges for users', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440023',
        'name' => 'Admin User',
        'email' => 'admin-roles@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee('Super Admin');
});

it('shows no role label for user without roles', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440024',
        'name' => 'Admin User',
        'email' => 'admin-norole@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440025',
        'name' => 'No Role User',
        'email' => 'norole@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee(__('messages.users.no_role'));
});

it('super admin sees impersonate button for other users', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440026',
        'name' => 'Admin Imp',
        'email' => 'admin-imp@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440027',
        'name' => 'Other User',
        'email' => 'other-imp@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users')
        ->assertStatus(200)
        ->assertSee(__('messages.impersonation.start'));
});

it('super admin does not see impersonate button for self', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440028',
        'name' => 'Solo Admin',
        'email' => 'solo-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $response = $this->actingAs($admin)
        ->get('/users')
        ->assertStatus(200);

    $content = $response->getContent();
    expect($content)->not->toContain('impersonation/start/550e8400-e29b-41d4-a716-446655440028');
});

it('non-super-admin does not see impersonate button', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Editor Imp',
        'Can view users',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $editor = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440029',
        'name' => 'Editor Imp User',
        'email' => 'editor-imp@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($editor->id, $role->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440030',
        'name' => 'Target Imp',
        'email' => 'target-imp@example.com',
    ]);

    $this->actingAs($editor)
        ->get('/users')
        ->assertStatus(200)
        ->assertDontSee(__('messages.impersonation.start'));
});

it('user without users.list.update does not see edit button', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Viewer NoEdit',
        'Can only view users',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440031',
        'name' => 'Viewer NoEdit',
        'email' => 'viewer-noedit@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440032',
        'name' => 'Target NoEdit',
        'email' => 'target-noedit@example.com',
    ]);

    $response = $this->actingAs($viewer)
        ->get('/users')
        ->assertStatus(200);

    $content = $response->getContent();
    expect($content)->not->toContain('aria-label="'.__('messages.users.edit_action').' Target NoEdit"');
});

it('user without users.list.delete does not see delete button', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Viewer NoDel',
        'Can only view users',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440033',
        'name' => 'Viewer NoDel',
        'email' => 'viewer-nodel@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440034',
        'name' => 'Target NoDel',
        'email' => 'target-nodel@example.com',
    ]);

    $response = $this->actingAs($viewer)
        ->get('/users')
        ->assertStatus(200);

    $content = $response->getContent();
    expect($content)->not->toContain('aria-label="'.__('messages.users.delete_action').' Target NoDel"');
});

it('user with full permissions sees all action buttons', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440035',
        'name' => 'Full Admin',
        'email' => 'full-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440036',
        'name' => 'Target Full',
        'email' => 'target-full@example.com',
    ]);

    $response = $this->actingAs($admin)
        ->get('/users')
        ->assertStatus(200);

    $content = $response->getContent();
    expect($content)
        ->toContain('aria-label="'.__('messages.users.edit_action').' Target Full"')
        ->toContain('aria-label="'.__('messages.users.delete_action').' Target Full"');
});

it('team-scoped user only sees teammates', function (): void {
    TeamModel::create([
        'id' => '00000000-0000-0000-0000-000000000090',
        'name' => 'Alpha',
        'slug' => 'alpha',
        'description' => '',
    ]);

    TeamModel::create([
        'id' => '00000000-0000-0000-0000-000000000091',
        'name' => 'Beta',
        'slug' => 'beta',
        'description' => '',
    ]);

    $role = $this->seedRoleWithPermissions(
        'Team Scoped',
        'Team scope on users',
        ['users.list.read' => 'team', 'users.roles.read' => 'team'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e50',
        'name' => 'Team Viewer',
        'email' => 'team-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000090', 'user_id' => $viewer->id, 'joined_at' => now()]);

    $teammate = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e51',
        'name' => 'Teammate Alpha',
        'email' => 'teammate-alpha@example.com',
    ]);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000090', 'user_id' => $teammate->id, 'joined_at' => now()]);

    $outsider = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e52',
        'name' => 'Outsider Beta',
        'email' => 'outsider-beta@example.com',
    ]);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000091', 'user_id' => $outsider->id, 'joined_at' => now()]);

    $this->actingAs($viewer)
        ->get('/users')
        ->assertOk()
        ->assertSee('Teammate Alpha')
        ->assertSee('Team Viewer')
        ->assertDontSee('Outsider Beta');
});

it('own-scoped user only sees themselves', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Own Scoped',
        'Own scope on users',
        ['users.list.read' => 'own'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e60',
        'name' => 'Own Viewer',
        'email' => 'own-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e61',
        'name' => 'Other Own User',
        'email' => 'other-own@example.com',
    ]);

    $this->actingAs($viewer)
        ->get('/users')
        ->assertOk()
        ->assertSee('Own Viewer')
        ->assertDontSee('Other Own User');
});
