<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function passwordRotationEnforcementUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'name' => 'Enforcement User',
        'email' => 'enforce-rotation@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('redirects to profile when password rotation is expired', function (): void {
    $userModel = passwordRotationEnforcementUser();

    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    $userModel->password_changed_at = CarbonImmutable::now()->subDays(200);
    $userModel->save();

    $this->actingAs($userModel)
        ->get('/users')
        ->assertRedirect(route('profile'));
});

it('allows profile when password rotation is expired', function (): void {
    $userModel = passwordRotationEnforcementUser();

    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    $userModel->password_changed_at = CarbonImmutable::now()->subDays(200);
    $userModel->save();

    $this->actingAs($userModel)
        ->get('/profile')
        ->assertOk();
});

it('allows password rotation settings when password rotation is expired', function (): void {
    $userModel = passwordRotationEnforcementUser();

    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    $userModel->password_changed_at = CarbonImmutable::now()->subDays(200);
    $userModel->save();

    $this->actingAs($userModel)
        ->get(route('settings.password-rotation'))
        ->assertRedirect(route('settings.index', ['tab' => 'password-rotation']));
});

it('allows other routes when password is only in the warning window', function (): void {
    $userModel = passwordRotationEnforcementUser();

    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    $userModel->password_changed_at = CarbonImmutable::now()->subDays(28);
    $userModel->save();

    $this->actingAs($userModel)
        ->get('/users')
        ->assertOk();
});
