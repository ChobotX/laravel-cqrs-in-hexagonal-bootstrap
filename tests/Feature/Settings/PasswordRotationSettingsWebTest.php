<?php

declare(strict_types=1);

use App\Contract\Bus\CommandBus;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Settings\ShowPasswordRotationSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdatePasswordRotationSettingsController;
use App\Presentation\Http\Request\Web\Settings\UpdatePasswordRotationSettingsRequest;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function passwordRotationSettingsWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Rotation Admin',
        'email' => 'rotation-admin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows password rotation settings page', function (): void {
    $userModel = passwordRotationSettingsWebUser();

    $this->actingAs($userModel)
        ->get(route('settings.index', ['tab' => 'password-rotation']))
        ->assertOk()
        ->assertSee(__('messages.settings.password_rotation_title'));
});

it('updates password rotation settings', function (): void {
    $userModel = passwordRotationSettingsWebUser();

    $this->actingAs($userModel)
        ->put(route('settings.password-rotation.update'), [
            'rotation_enabled' => '1',
            'max_age_days' => '45',
            'history_count' => '8',
        ])->assertRedirect(route('settings.index', ['tab' => 'password-rotation']));

    $row = DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->first();

    expect($row)->toBeInstanceOf(stdClass::class);

    $maxAgeDays = filter_var($row->max_age_days, FILTER_VALIDATE_INT);
    $historyCount = filter_var($row->history_count, FILTER_VALIDATE_INT);

    expect((bool) $row->rotation_enabled)->toBeTrue()
        ->and($maxAgeDays)->toBe(45)
        ->and($historyCount)->toBe(8);
});

it('requires settings.tenant.read to show password rotation page', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f01',
        'name' => 'No Perms',
        'email' => 'noperms-rotation@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->get(route('settings.password-rotation'))
        ->assertForbidden();
});

it('requires max_age_days when rotation is enabled', function (): void {
    $userModel = passwordRotationSettingsWebUser();

    $this->actingAs($userModel)
        ->put(route('settings.password-rotation.update'), [
            'rotation_enabled' => '1',
            'history_count' => '5',
        ])
        ->assertSessionHasErrors('max_age_days');
});

it('allows disabling rotation without max_age_days', function (): void {
    $userModel = passwordRotationSettingsWebUser();

    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    $this->actingAs($userModel)
        ->put(route('settings.password-rotation.update'), [
            'rotation_enabled' => '0',
            'history_count' => '6',
        ])
        ->assertRedirect(route('settings.index', ['tab' => 'password-rotation']))
        ->assertSessionHasNoErrors();

    $row = DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->first();

    expect($row)->toBeInstanceOf(stdClass::class);

    $historyCount = filter_var($row->history_count, FILTER_VALIDATE_INT);

    expect((bool) $row->rotation_enabled)->toBeFalse()
        ->and($historyCount)->toBe(6);
});

it('show redirects to settings password rotation tab', function (): void {
    $controller = new ShowPasswordRotationSettingsController;

    $redirectResponse = $controller();

    expect($redirectResponse->getTargetUrl())->toBe(route('settings.index', ['tab' => 'password-rotation']));
});

it('update aborts when tenant_id is missing from context', function (): void {
    Context::flush();
    $mock = Mockery::mock(CommandBus::class);
    $controller = new UpdatePasswordRotationSettingsController($mock);

    $updatePasswordRotationSettingsRequest = UpdatePasswordRotationSettingsRequest::create(
        '/settings/password-rotation',
        'PUT',
        [
            'rotation_enabled' => '0',
            'history_count' => '5',
        ],
    );

    $controller($updatePasswordRotationSettingsRequest);
})->throws(Symfony\Component\HttpKernel\Exception\HttpException::class);

it('requires settings.tenant.update to save password rotation settings', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f02',
        'name' => 'Read Only',
        'email' => 'readonly-rotation@test.com',
        'password' => Hash::make('password'),
    ]);

    $role = test()->seedRoleWithPermissions('Read settings', 'Read', ['settings.tenant.read' => 'all']);
    test()->assignRole($user->id, $role->id);

    $this->actingAs($user)
        ->put(route('settings.password-rotation.update'), [
            'rotation_enabled' => '0',
            'history_count' => '5',
        ])
        ->assertForbidden();
});
