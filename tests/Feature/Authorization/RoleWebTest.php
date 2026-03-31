<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function webUser(): UserModel
{
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440950', 'name' => 'Web Admin', 'email' => 'webadmin@test.com', 'password' => Hash::make('password')]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows role list page', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)->get('/roles')->assertOk();
});

it('shows create role form', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)->get('/roles/create')->assertOk();
});

it('creates a role via web', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)->post('/roles', [
        'name' => 'Web Role',
        'description' => 'Created from web',
        'permissions' => [],
    ])->assertRedirect('/roles');

    $this->assertDatabaseHas('roles', ['name' => 'Web Role']);
});

it('shows role detail page', function (): void {
    $userModel = webUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440960', 'name' => 'ViewRole', 'description' => 'Viewable', 'is_system' => false]);

    $response = $this->actingAs($userModel)->get('/roles/550e8400-e29b-41d4-a716-446655440960');
    $response->assertOk();

    $content = $response->getContent();
    expect($content)
        ->toContain('ViewRole')
        ->toContain('Viewable')
        ->toContain(__('messages.roles.permissions'));
});

it('shows edit role form', function (): void {
    $userModel = webUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440951', 'name' => 'ToEdit', 'description' => 'E', 'is_system' => false]);

    $this->actingAs($userModel)->get('/roles/550e8400-e29b-41d4-a716-446655440951/edit')->assertOk();
});

it('updates a role via web', function (): void {
    $userModel = webUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440952', 'name' => 'Old', 'description' => 'O', 'is_system' => false]);

    $this->actingAs($userModel)->put('/roles/550e8400-e29b-41d4-a716-446655440952', [
        'name' => 'New',
        'description' => 'Updated',
        'permissions' => [],
    ])->assertRedirect('/roles');
});

it('deletes a role via web', function (): void {
    $userModel = webUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440953', 'name' => 'Del', 'description' => 'D', 'is_system' => false]);

    $this->actingAs($userModel)->delete('/roles/550e8400-e29b-41d4-a716-446655440953')->assertRedirect('/roles');
});

it('shows user edit page with permissions section', function (): void {
    $userModel = webUser();
    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440954', 'name' => 'Target', 'email' => 'tperm@test.com']);

    $this->actingAs($userModel)->get(sprintf('/users/%s/edit', $target->id))
        ->assertOk()
        ->assertSee(__('messages.permissions.effective_permissions'));
});

it('starts impersonation via web', function (): void {
    $userModel = webUser();
    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440955', 'name' => 'Target', 'email' => 'timw@test.com']);

    $this->actingAs($userModel)->post('/impersonate/'.$target->id)->assertRedirect();
});

it('stops impersonation via web', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)->post('/stop-impersonation')->assertRedirect();
});

it('sorts roles by name ascending via query params', function (): void {
    $userModel = webUser();

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c070', 'name' => 'Zulu Role', 'description' => 'Z', 'is_system' => false]);
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c071', 'name' => 'Alpha Role', 'description' => 'A', 'is_system' => false]);

    $this->actingAs($userModel)
        ->get('/roles?sort=name&direction=asc')
        ->assertOk()
        ->assertSeeInOrder(['Alpha Role', 'Zulu Role']);
});

it('sorts roles by name descending via query params', function (): void {
    $userModel = webUser();

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c072', 'name' => 'Zulu Desc', 'description' => 'Z', 'is_system' => false]);
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c073', 'name' => 'Alpha Desc', 'description' => 'A', 'is_system' => false]);

    $this->actingAs($userModel)
        ->get('/roles?sort=name&direction=desc')
        ->assertOk()
        ->assertSeeInOrder(['Zulu Desc', 'Alpha Desc']);
});

it('ignores invalid sort column for roles', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)
        ->get('/roles?sort=invalid&direction=asc')
        ->assertOk();
});

it('redirects unauthenticated to login', function (): void {
    $this->get('/roles')->assertRedirect('/login?'.http_build_query(['redirect' => '/roles']));
});

it('redirects to page 1 when requested page exceeds total pages', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)
        ->get('/roles?page=5&per_page=15&sort=name&direction=asc')
        ->assertRedirect('/roles?page=1&per_page=15&sort=name&direction=asc');
});

it('creates a role with permissions via web', function (): void {
    $userModel = webUser();

    $this->actingAs($userModel)->post('/roles', [
        'name' => 'Perm Role',
        'description' => 'With permissions',
        'permissions' => [
            'users.list.read' => ['enabled' => '1', 'scope' => 'all'],
            'users.list.create' => ['enabled' => '1', 'scope' => 'team'],
        ],
    ])->assertRedirect('/roles');

    $this->assertDatabaseHas('roles', ['name' => 'Perm Role']);
    $this->assertDatabaseHas('role_permissions', ['module' => 'users', 'feature' => 'list', 'action' => 'read']);
});

it('updates a role with permissions via web', function (): void {
    $userModel = webUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440956', 'name' => 'UpdPerm', 'description' => 'O', 'is_system' => false]);

    $this->actingAs($userModel)->put('/roles/550e8400-e29b-41d4-a716-446655440956', [
        'name' => 'UpdPerm',
        'description' => 'Updated',
        'permissions' => [
            'users.list.update' => ['enabled' => '1', 'scope' => 'own'],
        ],
    ])->assertRedirect('/roles');
});

it('returns 403 when no permissions on web role list', function (): void {
    $userModel = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440958', 'name' => 'No Perms', 'email' => 'noperms-web@test.com', 'password' => Hash::make('password')]);

    $this->actingAs($userModel)->get('/roles')->assertForbidden();
});

it('returns 403 when no permissions on web user edit', function (): void {
    $userModel = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440959', 'name' => 'No Perms', 'email' => 'noperms-web2@test.com', 'password' => Hash::make('password')]);
    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440957', 'name' => 'T', 'email' => 'timpn@test.com']);

    $this->actingAs($userModel)->get(sprintf('/users/%s/edit', $target->id))->assertForbidden();
});
