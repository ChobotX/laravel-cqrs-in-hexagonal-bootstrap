<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('renders inline totp qr svg when user has pending totp secret', function (): void {
    $user = UserModel::create([
        'name' => 'Totp Qr User',
        'email' => 'totp-qr-view@example.com',
        'password' => Hash::make('password'),
        'totp_secret' => 'JBSWY3DPEHPK3PXP',
    ]);

    $this->actingAs($user)
        ->get(route('profile.two-factor'))
        ->assertOk()
        ->assertSee('<svg', false)
        ->assertSee(__('messages.settings.totp_qr_aria'), false)
        ->assertSee(__('messages.settings.totp_switch_label'))
        ->assertSee(__('messages.settings.confirm_totp_setup'))
        ->assertDontSee(__('messages.settings.cancel_totp_setup'))
        ->assertDontSee(__('messages.settings.totp_enabled_active_hint'));
});

it('hides totp confirm until backup codes are downloaded when pending codes exist', function (): void {
    $user = UserModel::create([
        'name' => 'Totp Gate User',
        'email' => 'totp-gate-view@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->from(route('profile.two-factor'))
        ->put(route('profile.two-factor.update'), [
            'action' => 'totp-save',
            'totp_two_factor_enabled' => '1',
        ])
        ->assertRedirect(route('profile.two-factor'));

    $beforeDownload = $this->actingAs($user)
        ->get(route('profile.two-factor'))
        ->assertOk()
        ->getContent();
    assert(is_string($beforeDownload));
    expect($beforeDownload)->toContain('data-own-two-factor-totp-confirm-visible="0"');
    expect((bool) preg_match(
        '/class="[^"]*\bhidden\b[^"]*"[\s\S]*?data-own-two-factor-totp-confirm-panel/',
        $beforeDownload,
    ))->toBeTrue();

    $this->actingAs($user)
        ->get(route('profile.two-factor.backup-codes.download'))
        ->assertOk();

    $afterDownload = $this->actingAs($user)
        ->get(route('profile.two-factor'))
        ->assertOk()
        ->getContent();
    assert(is_string($afterDownload));
    expect($afterDownload)->toContain('data-own-two-factor-totp-confirm-visible="1"');
    expect((bool) preg_match(
        '/class="[^"]*\bflex\b[^"]*\bflex-col\b[^"]*\bgap-5\b[^"]*"[\s\S]*?data-own-two-factor-totp-confirm-panel/',
        $afterDownload,
    ))->toBeTrue();
});

it('shows only authenticator toggle when totp is not configured', function (): void {
    $user = UserModel::create([
        'name' => 'No Totp User',
        'email' => 'no-totp-view@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->get(route('profile.two-factor'))
        ->assertOk()
        ->assertSee(__('messages.settings.totp_switch_label'))
        ->assertDontSee(__('messages.settings.totp_qr_hint'))
        ->assertDontSee(__('messages.settings.cancel_totp_setup'));
});

it('clears pending totp when authenticator switch is turned off', function (): void {
    $user = UserModel::create([
        'name' => 'Toggle Off Totp User',
        'email' => 'totp-toggle-off@example.com',
        'password' => Hash::make('password'),
        'totp_secret' => 'JBSWY3DPEHPK3PXP',
    ]);

    $this->actingAs($user)
        ->from(route('profile.two-factor'))
        ->put(route('profile.two-factor.update'), [
            'action' => 'totp-save',
            'totp_two_factor_enabled' => '0',
        ])
        ->assertRedirect(route('profile.two-factor'));

    $user->refresh();
    expect($user->totp_secret)->toBeNull();
});

it('shows active hint when totp is confirmed', function (): void {
    $user = UserModel::create([
        'name' => 'Confirmed Totp User',
        'email' => 'confirmed-totp-view@example.com',
        'password' => Hash::make('password'),
        'totp_secret' => 'JBSWY3DPEHPK3PXP',
        'totp_confirmed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('profile.two-factor'))
        ->assertOk()
        ->assertSee(__('messages.settings.totp_switch_label'))
        ->assertSee(__('messages.settings.totp_enabled_active_hint'))
        ->assertDontSee(__('messages.settings.cancel_totp_setup'))
        ->assertDontSee(__('messages.settings.totp_qr_hint'));
});
