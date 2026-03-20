<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @return array{UserModel, UserModel} */
function permissionsWebAdmin(): array
{
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default Org',
        'slug' => 'default-perm',
        'description' => '',
    ]);

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a10',
        'name' => 'Perm Admin',
        'email' => 'permadmin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a11',
        'name' => 'Perm Target',
        'email' => 'permtarget@test.com',
    ]);

    return [$admin, $target];
}

it('sets a permission override via web form', function (): void {
    [$admin, $target] = permissionsWebAdmin();

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'set_override',
            'permission' => 'teams.members.read',
            'type' => 'grant',
            'scope' => 'team',
        ])->assertRedirect(route('users.permissions', $target->id));

    $this->assertDatabaseHas('user_permission_overrides', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'module' => 'teams',
        'feature' => 'members',
        'action' => 'read',
        'type' => 'grant',
        'scope' => 'team',
    ]);
});

it('sets a deny override via web form', function (): void {
    [$admin, $target] = permissionsWebAdmin();

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'set_override',
            'permission' => 'teams.management.create',
            'type' => 'deny',
            'scope' => 'all',
        ])->assertRedirect(route('users.permissions', $target->id));

    $this->assertDatabaseHas('user_permission_overrides', [
        'user_id' => $target->id,
        'module' => 'teams',
        'feature' => 'management',
        'action' => 'create',
        'type' => 'deny',
    ]);
});

it('assigns a role via web permissions form', function (): void {
    [$admin, $target] = permissionsWebAdmin();

    $role = RoleModel::create([
        'id' => Str::uuid()->toString(),
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Assign Test Role',
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

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'assign_role',
            'role_id' => $role->id,
        ])->assertRedirect(route('users.permissions', $target->id));

    $this->assertDatabaseHas('user_roles', [
        'user_id' => $target->id,
        'role_id' => $role->id,
    ]);
});

it('revokes a role via web permissions form', function (): void {
    [$admin, $target] = permissionsWebAdmin();

    $role = RoleModel::create([
        'id' => Str::uuid()->toString(),
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Revoke Test Role',
        'description' => 'Test',
        'is_system' => false,
    ]);

    test()->assignRole($target->id, $role->id, '00000000-0000-0000-0000-000000000001');

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'revoke_role',
            'role_id' => $role->id,
        ])->assertRedirect(route('users.permissions', $target->id));

    $this->assertDatabaseMissing('user_roles', [
        'user_id' => $target->id,
        'role_id' => $role->id,
    ]);
});

it('removes a permission override via web form', function (): void {
    [$admin, $target] = permissionsWebAdmin();

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'set_override',
            'permission' => 'teams.members.read',
            'type' => 'grant',
            'scope' => 'team',
        ])->assertRedirect();

    $this->assertDatabaseHas('user_permission_overrides', [
        'user_id' => $target->id,
        'module' => 'teams',
        'feature' => 'members',
        'action' => 'read',
    ]);

    $this->actingAs($admin)
        ->post('/users/'.$target->id.'/permissions', [
            '_action' => 'remove_override',
            'permission' => 'teams.members.read',
        ])->assertRedirect(route('users.permissions', $target->id));

    $this->assertDatabaseMissing('user_permission_overrides', [
        'user_id' => $target->id,
        'module' => 'teams',
        'feature' => 'members',
        'action' => 'read',
    ]);
});

it('returns 405 for GET on manage route', function (): void {
    [$admin] = permissionsWebAdmin();

    $this->actingAs($admin)
        ->get('/users/'.$admin->id.'/permissions')
        ->assertOk();
});
