<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('returns 404 when no pending totp backup codes exist in session', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440914',
        'name' => 'No Pending Backup User',
        'email' => 'no-pending-backup@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->get(route('profile.two-factor.backup-codes.download'))
        ->assertNotFound();
});

it('streams pending totp backup codes after starting totp setup', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440915',
        'name' => 'Backup Download User',
        'email' => 'totp-backup-download@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->from(route('profile.two-factor'))
        ->put(route('profile.two-factor.update'), [
            'action' => 'totp-save',
            'totp_two_factor_enabled' => '1',
        ])
        ->assertRedirect(route('profile.two-factor'));

    $response = $this->actingAs($user)
        ->get(route('profile.two-factor.backup-codes.download'));

    $response->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8');

    $body = $response->getContent();
    expect($body)->not->toBeFalse();
    assert(is_string($body));
    expect(strlen($body))->toBeGreaterThan(10);
});
