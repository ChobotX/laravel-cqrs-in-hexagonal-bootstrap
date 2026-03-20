<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\Organization\TeamMemberModel;
use App\Infrastructure\Eloquent\Organization\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('shows the edit form pre-filled', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440040',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440041',
        'name' => 'Target User',
        'email' => 'target@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users/550e8400-e29b-41d4-a716-446655440041/edit')
        ->assertStatus(200)
        ->assertSee('Target User')
        ->assertSee('target@example.com');
});

it('updates name and email', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440042',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440043',
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->actingAs($admin)
        ->put('/users/550e8400-e29b-41d4-a716-446655440043', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440043',
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

it('updates password when provided', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440044',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440045',
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->actingAs($admin)
        ->put('/users/550e8400-e29b-41d4-a716-446655440045', [
            'name' => 'Target User',
            'email' => 'target@example.com',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertRedirect('/users');

    $updated = UserModel::find('550e8400-e29b-41d4-a716-446655440045');
    expect(Hash::check('newpassword1', $updated->password))->toBeTrue();
});

it('keeps existing password when blank', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440046',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440047',
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('keepme'),
    ]);

    $this->actingAs($admin)
        ->put('/users/550e8400-e29b-41d4-a716-446655440047', [
            'name' => 'Updated Name',
            'email' => 'target@example.com',
        ])->assertRedirect('/users');

    $updated = UserModel::find('550e8400-e29b-41d4-a716-446655440047');
    expect(Hash::check('keepme', $updated->password))->toBeTrue();
});

it('returns 404 for missing user', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440048',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $this->actingAs($admin)
        ->get('/users/550e8400-e29b-41d4-a716-446655440099/edit')
        ->assertStatus(404);
});

it('redirects unauthenticated user', function (): void {
    $this->get('/users/550e8400-e29b-41d4-a716-446655440041/edit')
        ->assertRedirect('/login');
});

it('shows role selector with users.roles.read permission', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440060',
        'name' => 'Admin Roles',
        'email' => 'admin-roles-edit@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440061',
        'name' => 'Target Roles',
        'email' => 'target-roles@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users/550e8400-e29b-41d4-a716-446655440061/edit')
        ->assertStatus(200)
        ->assertSee('data-input-name="roles[]"', false);
});

it('hides role selector without users.roles.read permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Basic Editor',
        'Can edit users but not roles',
        ['users.list.read' => 'all', 'users.list.update' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440062',
        'name' => 'No Roles Perm',
        'email' => 'noroles-perm@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id, '00000000-0000-0000-0000-000000000001');

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440063',
        'name' => 'Target No Perm',
        'email' => 'target-noperm@example.com',
    ]);

    $this->actingAs($user)
        ->get('/users/550e8400-e29b-41d4-a716-446655440063/edit')
        ->assertStatus(200)
        ->assertDontSee('data-input-name="roles[]"', false);
});

it('assigns role via form submission', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440064',
        'name' => 'Admin Assign',
        'email' => 'admin-assign@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440065',
        'name' => 'Target Assign',
        'email' => 'target-assign@example.com',
    ]);

    $editorRole = RoleModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440070',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Test Editor',
        'description' => 'Editor',
        'is_system' => false,
    ]);

    RolePermissionModel::create([
        'id' => Str::uuid()->toString(),
        'role_id' => $editorRole->id,
        'module' => 'users',
        'feature' => 'list',
        'action' => 'read',
        'scope' => 'all',
    ]);

    $this->actingAs($admin)
        ->put('/users/550e8400-e29b-41d4-a716-446655440065', [
            'name' => 'Target Assign',
            'email' => 'target-assign@example.com',
            'roles' => [$editorRole->id],
        ])->assertRedirect('/users');

    $this->assertDatabaseHas('user_roles', [
        'user_id' => '550e8400-e29b-41d4-a716-446655440065',
        'role_id' => $editorRole->id,
    ]);
});

it('non-super-admin cannot assign system role via form', function (): void {
    $this->seedSuperAdminRole();
    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Full Editor',
        'Can manage users and roles',
        [
            'users.list.read' => 'all',
            'users.list.create' => 'all',
            'users.list.update' => 'all',
            'users.list.delete' => 'all',
            'users.roles.read' => 'all',
            'users.roles.update' => 'all',
        ],
    );

    $editor = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440066',
        'name' => 'Editor User',
        'email' => 'editor-sys@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($editor->id, $role->id, '00000000-0000-0000-0000-000000000001');

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440067',
        'name' => 'Target Sys',
        'email' => 'target-sys@example.com',
    ]);

    $systemRole = RoleModel::where('is_system', true)->first();

    $this->actingAs($editor)
        ->put('/users/550e8400-e29b-41d4-a716-446655440067', [
            'name' => 'Target Sys',
            'email' => 'target-sys@example.com',
            'roles' => [$systemRole->id],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('user_roles', [
        'user_id' => '550e8400-e29b-41d4-a716-446655440067',
        'role_id' => $systemRole->id,
    ]);
});

it('revokes role via form submission when removed from selection', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440080',
        'name' => 'Admin Revoke',
        'email' => 'admin-revoke@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $targetUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440081',
        'name' => 'Target Revoke',
        'email' => 'target-revoke@example.com',
    ]);

    $editorRole = RoleModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440082',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Revoke Editor',
        'description' => 'Editor',
        'is_system' => false,
    ]);

    RolePermissionModel::create([
        'id' => Str::uuid()->toString(),
        'role_id' => $editorRole->id,
        'module' => 'users',
        'feature' => 'list',
        'action' => 'read',
        'scope' => 'all',
    ]);

    $this->assignRole($targetUser->id, $editorRole->id, '00000000-0000-0000-0000-000000000001');

    $this->assertDatabaseHas('user_roles', [
        'user_id' => $targetUser->id,
        'role_id' => $editorRole->id,
    ]);

    $this->actingAs($admin)
        ->put('/users/'.$targetUser->id, [
            'name' => 'Target Revoke',
            'email' => 'target-revoke@example.com',
            'roles' => [],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('user_roles', [
        'user_id' => $targetUser->id,
        'role_id' => $editorRole->id,
    ]);
});

it('skips role sync without users.roles.update permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'No Role Perm',
        'Can update users only',
        ['users.list.read' => 'all', 'users.list.update' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440083',
        'name' => 'No Sync User',
        'email' => 'nosync@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id, '00000000-0000-0000-0000-000000000001');

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440084',
        'name' => 'Target NoSync',
        'email' => 'target-nosync@example.com',
    ]);

    $this->actingAs($user)
        ->put('/users/550e8400-e29b-41d4-a716-446655440084', [
            'name' => 'Target NoSync Updated',
            'email' => 'target-nosync@example.com',
            'roles' => ['00000000-0000-0000-0000-000000000099'],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('user_roles', [
        'user_id' => '550e8400-e29b-41d4-a716-446655440084',
    ]);
});

it('non-super-admin cannot assign role with wider permissions', function (): void {
    $narrowRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Narrow Editor',
        'Limited permissions',
        [
            'users.list.read' => 'all',
            'users.list.update' => 'all',
            'users.roles.read' => 'all',
            'users.roles.update' => 'all',
        ],
    );

    $wideRole = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Wide Admin',
        'Full permissions',
        ['users.list.read' => 'all', 'users.list.delete' => 'all'],
    );

    $editor = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440068',
        'name' => 'Narrow User',
        'email' => 'narrow@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($editor->id, $narrowRole->id, '00000000-0000-0000-0000-000000000001');

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440069',
        'name' => 'Target Wide',
        'email' => 'target-wide@example.com',
    ]);

    $this->actingAs($editor)
        ->put('/users/550e8400-e29b-41d4-a716-446655440069', [
            'name' => 'Target Wide',
            'email' => 'target-wide@example.com',
            'roles' => [$wideRole->id],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('user_roles', [
        'user_id' => '550e8400-e29b-41d4-a716-446655440069',
        'role_id' => $wideRole->id,
    ]);
});

it('shows organization selector with organizations.members.update permission', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440090',
        'name' => 'Admin Org',
        'email' => 'admin-org-edit@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000010',
        'name' => 'Test Org',
        'slug' => 'test-org-edit',
        'description' => 'Test',
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440091',
        'name' => 'Target Org',
        'email' => 'target-org@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users/550e8400-e29b-41d4-a716-446655440091/edit')
        ->assertStatus(200)
        ->assertSee('data-search-url=', false)
        ->assertSee('data-input-name="organizations[]"', false);
});

it('hides organization selector without organizations.members.update permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'No Org Perm',
        'Can edit users but not org members',
        ['users.list.read' => 'all', 'users.list.update' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440092',
        'name' => 'No Org Perm User',
        'email' => 'noorg-perm@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id, '00000000-0000-0000-0000-000000000001');

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440093',
        'name' => 'Target No Org Perm',
        'email' => 'target-noorg-perm@example.com',
    ]);

    $this->actingAs($user)
        ->get('/users/550e8400-e29b-41d4-a716-446655440093/edit')
        ->assertStatus(200)
        ->assertDontSee('data-input-name="organizations[]"', false);
});

it('adds organization via form submission', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440094',
        'name' => 'Admin Add Org',
        'email' => 'admin-addorg@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000011',
        'name' => 'Add Org',
        'slug' => 'add-org-edit',
        'description' => 'To add',
    ]);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440095',
        'name' => 'Target Add Org',
        'email' => 'target-addorg@example.com',
    ]);

    $this->actingAs($admin)
        ->put('/users/'.$target->id, [
            'name' => 'Target Add Org',
            'email' => 'target-addorg@example.com',
            'organizations' => ['00000000-0000-0000-0000-000000000011'],
        ])->assertRedirect('/users');

    $this->assertDatabaseHas('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000011',
    ]);
});

it('removes organization via form submission', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440096',
        'name' => 'Admin Rm Org',
        'email' => 'admin-rmorg@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000012',
        'name' => 'Rm Org',
        'slug' => 'rm-org-edit',
        'description' => 'To remove',
    ]);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440097',
        'name' => 'Target Rm Org',
        'email' => 'target-rmorg@example.com',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000012',
        'joined_at' => now(),
    ]);

    $this->assertDatabaseHas('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000012',
    ]);

    $this->actingAs($admin)
        ->put('/users/'.$target->id, [
            'name' => 'Target Rm Org',
            'email' => 'target-rmorg@example.com',
            'organizations' => [],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000012',
    ]);
});

it('adds team membership via form submission', function (): void {
    $this->seedSuperAdminRole();

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default Org',
        'slug' => 'default-addteam',
        'description' => '',
    ]);

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440101',
        'name' => 'Admin Add Team',
        'email' => 'admin-addteam@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440102',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Team to Add',
        'slug' => 'team-to-add',
        'description' => '',
    ]);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440103',
        'name' => 'Target Add Team',
        'email' => 'target-addteam@example.com',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $this->actingAs($admin)
        ->put('/users/'.$target->id, [
            'name' => 'Target Add Team',
            'email' => 'target-addteam@example.com',
            'organizations' => ['00000000-0000-0000-0000-000000000001'],
            'teams' => ['550e8400-e29b-41d4-a716-446655440102'],
        ])->assertRedirect('/users');

    $this->assertDatabaseHas('team_members', [
        'user_id' => $target->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440102',
    ]);
});

it('removes team membership via form submission', function (): void {
    $this->seedSuperAdminRole();

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default Org',
        'slug' => 'default-rmteam',
        'description' => '',
    ]);

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440104',
        'name' => 'Admin Rm Team',
        'email' => 'admin-rmteam@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440105',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Team to Remove',
        'slug' => 'team-to-remove',
        'description' => '',
    ]);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440106',
        'name' => 'Target Rm Team',
        'email' => 'target-rmteam@example.com',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    TeamMemberModel::create([
        'team_id' => '550e8400-e29b-41d4-a716-446655440105',
        'user_id' => $target->id,
        'joined_at' => now(),
    ]);

    $this->actingAs($admin)
        ->put('/users/'.$target->id, [
            'name' => 'Target Rm Team',
            'email' => 'target-rmteam@example.com',
            'organizations' => ['00000000-0000-0000-0000-000000000001'],
            'teams' => [],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('team_members', [
        'user_id' => $target->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440105',
    ]);
});

it('skips org sync without organizations.members.update permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'No Org Sync Perm',
        'Can update users only',
        ['users.list.read' => 'all', 'users.list.update' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440098',
        'name' => 'No Org Sync User',
        'email' => 'noorgsync@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id, '00000000-0000-0000-0000-000000000001');

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000013',
        'name' => 'No Sync Org',
        'slug' => 'nosync-org-edit',
        'description' => 'Should not be added',
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440100',
        'name' => 'Target No Org Sync',
        'email' => 'target-noorgsync@example.com',
    ]);

    $this->actingAs($user)
        ->put('/users/550e8400-e29b-41d4-a716-446655440100', [
            'name' => 'Target No Org Sync Updated',
            'email' => 'target-noorgsync@example.com',
            'organizations' => ['00000000-0000-0000-0000-000000000013'],
        ])->assertRedirect('/users');

    $this->assertDatabaseMissing('organization_members', [
        'user_id' => '550e8400-e29b-41d4-a716-446655440100',
    ]);
});
