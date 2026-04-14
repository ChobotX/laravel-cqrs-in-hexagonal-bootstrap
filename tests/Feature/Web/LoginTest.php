<?php

declare(strict_types=1);

use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;
use App\Infrastructure\Eloquent\User\UserModel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('shows the login page', function (): void {
    $this->get('/login')
        ->assertStatus(200)
        ->assertSee('Login');
});

it('logs in with valid credentials', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440001',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/users');

    $this->assertAuthenticated();
});

it('rejects invalid credentials', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440002',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ])->assertRedirect()
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirects authenticated user away from login', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440003',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect();
});

it('redirects to intended URL after login', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440004',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $redirectUrl = $this->get('/roles')->headers->get('Location');
    expect($redirectUrl)->toContain('/login?redirect=');

    $this->get($redirectUrl);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/roles');
});

it('validates login input', function (): void {
    $this->post('/login', [
        'email' => '',
        'password' => '',
    ])->assertSessionHasErrors(['email', 'password']);
});

it('flashes password_rotation when policy is enabled and password is in the warning window', function (): void {
    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440010',
        'name' => 'Warn Login',
        'email' => 'warn-login@example.com',
        'password' => Hash::make('password123'),
        'password_changed_at' => CarbonImmutable::now()->subDays(28),
    ]);

    $this->post('/login', [
        'email' => 'warn-login@example.com',
        'password' => 'password123',
    ])
        ->assertRedirect('/users')
        ->assertSessionHas('password_rotation', PasswordRotationUiStatus::WARNING);
});

it('flashes password_rotation expired when policy is enabled and password age exceeded', function (): void {
    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->update([
        'rotation_enabled' => true,
        'max_age_days' => 30,
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440011',
        'name' => 'Expired Login',
        'email' => 'expired-login@example.com',
        'password' => Hash::make('password123'),
        'password_changed_at' => CarbonImmutable::now()->subDays(200),
    ]);

    $this->post('/login', [
        'email' => 'expired-login@example.com',
        'password' => 'password123',
    ])
        ->assertRedirect('/users')
        ->assertSessionHas('password_rotation', PasswordRotationUiStatus::EXPIRED);
});

it('redirects to two-factor challenge when tenant requires two-factor and user already configured method', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440012',
        'name' => '2FA Challenge Login',
        'email' => '2fa-challenge@example.com',
        'password' => Hash::make('password123'),
        'email_two_factor_enabled' => true,
        'email_two_factor_confirmed_at' => CarbonImmutable::now()->subDay(),
    ]);

    $this->post('/login', [
        'email' => '2fa-challenge@example.com',
        'password' => 'password123',
    ])
        ->assertRedirect(route('two-factor.challenge'))
        ->assertSessionHas('two_factor_passed', false);
});

it('redirects to own two-factor setup when tenant requires two-factor and user has no method', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update([
        'required_for_all_users' => true,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440013',
        'name' => '2FA Setup Login',
        'email' => '2fa-setup@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => '2fa-setup@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('profile.two-factor'));
});
