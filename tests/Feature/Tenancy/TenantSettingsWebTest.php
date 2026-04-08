<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

function settingsWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e00',
        'name' => 'Settings Admin',
        'email' => 'settingsadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows tenant settings page', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->get('/settings')
        ->assertOk()
        ->assertSee(__('messages.settings.title'));
});

it('updates tenant name', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Updated Tenant Name',
        ])->assertRedirect(route('settings.index'));

    $tenant = TenantModel::findOrFail(test()->tenantId());
    expect($tenant->name)->toBe('Updated Tenant Name');
});

it('uploads a logo', function (): void {
    Storage::fake('public');
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'logo' => UploadedFile::fake()->image('logo.png', 100, 100),
        ])->assertRedirect(route('settings.index'));

    $tenant = TenantModel::findOrFail(test()->tenantId());
    expect($tenant->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($tenant->logo_path);
});

it('removes a logo', function (): void {
    Storage::fake('public');
    $userModel = settingsWebUser();

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'logo' => $file,
        ]);

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'remove_logo' => '1',
        ])->assertRedirect(route('settings.index'));

    $tenant = TenantModel::findOrFail(test()->tenantId());
    expect($tenant->logo_path)->toBeNull();
});

it('validates name is required', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => '',
        ])->assertSessionHasErrors('name');
});

it('validates logo must be an image', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'logo' => UploadedFile::fake()->create('document.pdf', 100),
        ])->assertSessionHasErrors('logo');
});

it('validates logo max size', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'logo' => UploadedFile::fake()->image('huge.png')->size(3000),
        ])->assertSessionHasErrors('logo');
});

it('requires authentication', function (): void {
    $this->get('/settings')->assertRedirect();
    $this->put('/settings', ['name' => 'Test'])->assertRedirect();
});

it('requires settings.tenant.read permission for show', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e01',
        'name' => 'No Perms',
        'email' => 'noperms-settings@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->get('/settings')
        ->assertForbidden();
});

it('requires settings.tenant.update permission for update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e02',
        'name' => 'No Perms',
        'email' => 'noperms-settings-update@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->put('/settings', ['name' => 'Test'])
        ->assertForbidden();
});
