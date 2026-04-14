<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Settings\UpdateTwoFactorSettingsController;
use App\Presentation\Http\Request\Web\Settings\UpdateTwoFactorSettingsRequest;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function twoFactorSettingsWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f0',
        'name' => '2FA Admin',
        'email' => 'two-factor-admin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows two-factor tenant settings tab', function (): void {
    $this->actingAs(twoFactorSettingsWebUser())
        ->get(route('settings.index', ['tab' => 'two-factor']))
        ->assertOk()
        ->assertSee(__('messages.settings.two_factor_title'));
});

it('updates two-factor tenant settings', function (): void {
    $this->actingAs(twoFactorSettingsWebUser())
        ->put(route('settings.two-factor.update'), [
            'required_for_all_users' => '1',
            'email_otp_enabled' => '1',
            'totp_enabled' => '0',
        ])->assertRedirect(route('settings.index', ['tab' => 'two-factor']));

    $row = DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->first();
    expect($row)->toBeInstanceOf(stdClass::class)
        ->and((bool) $row->required_for_all_users)->toBeTrue()
        ->and((bool) $row->email_otp_enabled)->toBeTrue()
        ->and((bool) $row->totp_enabled)->toBeFalse();
});

it('update controller aborts when tenant_id is missing from context', function (): void {
    Context::flush();
    $mock = Mockery::mock(App\Application\Bus\CommandBus::class);
    $controller = new UpdateTwoFactorSettingsController($mock);
    $updateTwoFactorSettingsRequest = UpdateTwoFactorSettingsRequest::create('/settings/two-factor', 'PUT', [
        'required_for_all_users' => '1',
        'email_otp_enabled' => '1',
        'totp_enabled' => '1',
    ]);

    $controller($updateTwoFactorSettingsRequest);
})->throws(Symfony\Component\HttpKernel\Exception\HttpException::class);
