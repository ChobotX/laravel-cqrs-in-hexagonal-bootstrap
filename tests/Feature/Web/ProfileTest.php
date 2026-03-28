<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('shows profile page for authenticated user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d00',
        'name' => 'Profile User',
        'email' => 'profile@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profile User')
        ->assertSee(__('messages.profile.subtitle'));
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/profile')
        ->assertRedirect('/login');
});

it('updates name and password without special permissions', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d01',
        'name' => 'Old Name',
        'email' => 'basic@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'New Name',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and(Hash::check('new-password', $user->password))->toBeTrue();
});

it('keeps email unchanged without users.list.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d02',
        'name' => 'Keep Email',
        'email' => 'keep@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Updated Name',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('keep@example.com');
});

it('allows email change with users.list.update permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d03',
        'name' => 'Admin Profile',
        'email' => 'admin-profile@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Admin Profile',
            'email' => 'new-admin@example.com',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->email)->toBe('new-admin@example.com');
});

it('hides email field without users.list.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d04',
        'name' => 'No Email Edit',
        'email' => 'noedit@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->not->toContain('id="email"');
});

it('shows email field with users.list.update', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d05',
        'name' => 'Can Edit Email',
        'email' => 'canedit@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('id="email"');
});

it('shows effective permissions for all users', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d06',
        'name' => 'Perm Viewer',
        'email' => 'permview@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee(__('messages.permissions.effective_permissions'));
});

it('hides override controls without users.roles.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d07',
        'name' => 'No Override',
        'email' => 'nooverride@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->not->toContain('id="override-permission"');
});

it('shows override controls with users.roles.update', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d08',
        'name' => 'Override Admin',
        'email' => 'overrideadmin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('id="override-permission"');
});

it('hides role selector without users.roles.read', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d09',
        'name' => 'No Roles',
        'email' => 'noroles@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->not->toContain('data-chip-selector');
});

it('admin changes password via profile', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d20',
        'name' => 'Admin Pass',
        'email' => 'adminpass@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Admin Pass',
            'email' => 'adminpass@example.com',
            'password' => 'new-secure-pass',
            'password_confirmation' => 'new-secure-pass',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect(Hash::check('new-secure-pass', $user->password))->toBeTrue();
});

it('syncs roles via profile when user has roles permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d21',
        'name' => 'Role Sync',
        'email' => 'rolesync@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $role = RoleModel::create([
        'id' => Str::uuid()->toString(),
        'name' => 'Profile Role',
        'description' => 'Test',
        'is_system' => false,
    ]);
    RolePermissionModel::create([
        'id' => Str::uuid()->toString(),
        'role_id' => $role->id,
        'module' => 'teams',
        'feature' => 'management',
        'action' => 'read',
        'scope' => 'all',
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Role Sync',
            'email' => 'rolesync@example.com',
            'roles' => [$role->id],
        ])
        ->assertRedirect('/profile');

    $this->assertDatabaseHas('user_roles', [
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);
});

it('syncs teams via profile when user has teams permission', function (): void {
    $superAdminRole = $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d22',
        'name' => 'Team Sync',
        'email' => 'teamsync@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    TeamModel::create([
        'id' => '00000000-0000-0000-0000-000000000d01',
        'name' => 'Profile Team',
        'slug' => 'profile-team',
        'description' => '',
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Team Sync',
            'email' => 'teamsync@example.com',
            'roles' => [$superAdminRole->id],
            'teams' => ['00000000-0000-0000-0000-000000000d01'],
        ])
        ->assertRedirect('/profile');

    $this->assertDatabaseHas('team_members', [
        'user_id' => $user->id,
        'team_id' => '00000000-0000-0000-0000-000000000d01',
    ]);
});

it('removes team membership via profile', function (): void {
    $superAdminRole = $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d23',
        'name' => 'Team Remove',
        'email' => 'teamremove@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    TeamModel::create(['id' => '00000000-0000-0000-0000-000000000d02', 'name' => 'RemoveMe', 'slug' => 'remove-me', 'description' => '']);
    App\Infrastructure\Eloquent\Team\TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000d02', 'user_id' => $user->id, 'joined_at' => now()]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Team Remove',
            'email' => 'teamremove@example.com',
            'roles' => [$superAdminRole->id],
            'teams' => [],
        ])
        ->assertRedirect('/profile');

    $this->assertDatabaseMissing('team_members', [
        'user_id' => $user->id,
        'team_id' => '00000000-0000-0000-0000-000000000d02',
    ]);
});

it('shows role selector with users.roles.read', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d10',
        'name' => 'Has Roles',
        'email' => 'hasroles@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('data-chip-selector');
});
