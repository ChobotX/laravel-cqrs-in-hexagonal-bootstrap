<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function orgWebUser(): UserModel
{
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default',
        'slug' => 'default',
        'description' => 'Default org',
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440970',
        'name' => 'Org Admin',
        'email' => 'orgadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows organization list page', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->get('/organizations')->assertOk();
});

it('shows create organization form', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->get('/organizations/create')->assertOk();
});

it('creates an organization via web', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->post('/organizations', [
        'name' => 'New Org',
        'slug' => 'new-org',
        'description' => 'Test org',
    ])->assertRedirect('/organizations');

    $this->assertDatabaseHas('organizations', ['name' => 'New Org', 'slug' => 'new-org']);
});

it('shows organization detail page', function (): void {
    $userModel = orgWebUser();

    $response = $this->actingAs($userModel)->get('/organizations/00000000-0000-0000-0000-000000000001');
    $response->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('Default');
});

it('shows edit organization form', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->get('/organizations/00000000-0000-0000-0000-000000000001/edit')->assertOk();
});

it('updates an organization via web', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->put('/organizations/00000000-0000-0000-0000-000000000001', [
        'name' => 'Updated',
        'slug' => 'updated',
        'description' => 'Updated description',
    ])->assertRedirect('/organizations');

    $this->assertDatabaseHas('organizations', ['name' => 'Updated', 'slug' => 'updated']);
});

it('deletes an organization via web', function (): void {
    $userModel = orgWebUser();

    $this->actingAs($userModel)->delete('/organizations/00000000-0000-0000-0000-000000000001')->assertRedirect('/organizations');

    $this->assertSoftDeleted('organizations', ['id' => '00000000-0000-0000-0000-000000000001']);
});

it('redirects unauthenticated to login', function (): void {
    $this->get('/organizations')->assertRedirect('/login');
});
