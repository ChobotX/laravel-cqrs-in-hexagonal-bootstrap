<?php

declare(strict_types=1);

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Settings\ShowTenantSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateTenantSettingsController;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use App\Presentation\Http\Request\Web\Settings\UpdateTenantSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Context;
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
            'display_timezone' => '',
        ])->assertRedirect(route('settings.index'));

    expect(TenantPreferenceModel::readDisplayName())->toBe('Updated Tenant Name');
});

it('uploads a logo', function (): void {
    Storage::fake('public');
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => '',
            'logo' => UploadedFile::fake()->image('logo.png', 100, 100),
        ])->assertRedirect(route('settings.index'));

    $path = TenantPreferenceModel::readLogoPath();
    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);
});

it('removes a logo', function (): void {
    Storage::fake('public');
    $userModel = settingsWebUser();

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => '',
            'logo' => $file,
        ]);

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => '',
            'remove_logo' => '1',
        ])->assertRedirect(route('settings.index'));

    expect(TenantPreferenceModel::readLogoPath())->toBeNull();
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
            'display_timezone' => '',
            'logo' => UploadedFile::fake()->create('document.pdf', 100),
        ])->assertSessionHasErrors('logo');
});

it('validates logo max size', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => '',
            'logo' => UploadedFile::fake()->image('huge.png')->size(3000),
        ])->assertSessionHasErrors('logo');
});

it('requires authentication', function (): void {
    $this->get('/settings')->assertRedirect();
    $this->put('/settings', ['name' => 'Test', 'display_timezone' => ''])->assertRedirect();
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

it('show aborts when tenant_id is missing from context', function (): void {
    Context::flush();
    $mock = Mockery::mock(QueryBus::class);
    $mock->shouldReceive('dispatch')->andReturn(
        new TenantSettings('Tenant', null, null),
        new PasswordRotationSettings(false, null, 5),
    );
    $controller = new ShowTenantSettingsController($mock);

    $controller(ShowTenantSettingsRequest::createFromBase(Request::create('/settings', 'GET')));
})->throws(Symfony\Component\HttpKernel\Exception\HttpException::class);

it('update aborts when tenant_id is missing from context', function (): void {
    Context::flush();
    $mock = Mockery::mock(CommandBus::class);
    $controller = new UpdateTenantSettingsController($mock);

    $updateTenantSettingsRequest = UpdateTenantSettingsRequest::create('/settings', 'PUT', ['name' => 'Test']);
    $controller($updateTenantSettingsRequest);
})->throws(Symfony\Component\HttpKernel\Exception\HttpException::class);

it('requires settings.tenant.update permission for update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e02',
        'name' => 'No Perms',
        'email' => 'noperms-settings-update@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->put('/settings', ['name' => 'Test', 'display_timezone' => ''])
        ->assertForbidden();
});

it('persists display timezone from settings form', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => 'UTC',
        ])->assertRedirect(route('settings.index'));

    expect(TenantPreferenceModel::readDisplayTimezone())->toBe('UTC');
});

it('validates display timezone is a known IANA identifier', function (): void {
    $userModel = settingsWebUser();

    $this->actingAs($userModel)
        ->put('/settings', [
            'name' => 'Test Tenant',
            'display_timezone' => 'Not/A/Zone',
        ])->assertSessionHasErrors('display_timezone');
});
