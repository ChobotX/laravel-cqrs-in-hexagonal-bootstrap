<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Route;

it('passes through for closure routes without controller class', function (): void {
    Route::middleware(['web', 'auth'])->get('/test-closure-route', fn (): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response('ok'));

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440599',
        'name' => 'Test User',
        'email' => 'middleware-closure@test.com',
    ]);

    $this->actingAs($user)
        ->get('/test-closure-route')
        ->assertOk();
});

it('allows access when controller has SkipPermissionCheck attribute', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440600',
        'name' => 'User',
        'email' => 'middleware-skip@test.com',
    ]);

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect();
});

it('allows access when user has the required permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440601',
        'name' => 'Admin User',
        'email' => 'middleware-hasperm@test.com',
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/roles/create')
        ->assertOk();
});

it('returns 403 when user lacks the required permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440602',
        'name' => 'Viewer',
        'email' => 'middleware-noaccess@test.com',
    ]);

    $this->actingAs($user)
        ->get('/roles/create')
        ->assertForbidden();
});

it('returns 403 for edit role when user lacks permission', function (): void {
    $orgId = '00000000-0000-0000-0000-000000000001';
    $role = $this->seedRoleWithPermissions($orgId, 'Test Role', 'desc', []);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440603',
        'name' => 'Viewer',
        'email' => 'middleware-editrole@test.com',
    ]);

    $this->actingAs($user)
        ->get(sprintf('/roles/%s/edit', $role->id))
        ->assertForbidden();
});

it('returns 403 for create user when user lacks permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440604',
        'name' => 'Viewer',
        'email' => 'middleware-createuser@test.com',
    ]);

    $this->actingAs($user)
        ->get('/users/create')
        ->assertForbidden();
});

it('returns 403 for edit user when user lacks permission', function (): void {
    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440605',
        'name' => 'Target User',
        'email' => 'middleware-target@test.com',
    ]);

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440606',
        'name' => 'Viewer',
        'email' => 'middleware-edituser@test.com',
    ]);

    $this->actingAs($viewer)
        ->get(sprintf('/users/%s/edit', $target->id))
        ->assertForbidden();
});

it('returns 403 when organization context is null', function (): void {
    OrganizationModel::create(['id' => '00000000-0000-0000-0000-000000000001', 'name' => 'Org', 'slug' => 'org', 'description' => '']);
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440607',
        'name' => 'No Perms',
        'email' => 'middleware-noorg@test.com',
    ]);
    OrganizationMemberModel::create(['user_id' => $user->id, 'organization_id' => '00000000-0000-0000-0000-000000000001', 'joined_at' => now()]);

    $this->actingAs($user)
        ->get('/roles/create')
        ->assertForbidden();
});
