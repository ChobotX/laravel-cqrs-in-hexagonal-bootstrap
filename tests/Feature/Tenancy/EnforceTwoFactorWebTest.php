<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('redirects to two-factor challenge when tenant requires 2fa', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f1',
        'name' => '2FA Enforced',
        'email' => 'two-factor-enforced@test.com',
        'password' => Hash::make('password'),
        'email_two_factor_enabled' => true,
        'email_two_factor_confirmed_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->withSession(['two_factor_passed' => false])
        ->get('/users')
        ->assertRedirect(route('two-factor.challenge'));
});

it('redirects to own setup when tenant requires 2fa and user has no configured method', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f2',
        'name' => '2FA Setup Required',
        'email' => 'two-factor-setup-required@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->withSession(['two_factor_passed' => false])
        ->get('/users')
        ->assertRedirect(route('profile.two-factor'));
});

it('allows request when two_factor_passed session flag is true', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f3',
        'name' => '2FA Session Passed',
        'email' => 'two-factor-session-passed@test.com',
        'password' => Hash::make('password'),
        'email_two_factor_enabled' => true,
        'email_two_factor_confirmed_at' => now(),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->withSession(['two_factor_passed' => true])
        ->get('/users')
        ->assertOk();
});

it('allows request when two_factor_passed flag is absent', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f4',
        'name' => '2FA Session Missing',
        'email' => 'two-factor-session-missing@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertOk();
});

it('allows request when policy does not require two-factor even with false session flag', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => false,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-4466554400f5',
        'name' => '2FA Not Required',
        'email' => 'two-factor-not-required@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->withSession(['two_factor_passed' => false])
        ->get('/users')
        ->assertOk();
});
