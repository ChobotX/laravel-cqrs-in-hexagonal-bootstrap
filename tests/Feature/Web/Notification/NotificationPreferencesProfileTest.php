<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Notification\NotificationPreferenceModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('syncs notification preferences via profile update', function (): void {
    $userModel = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d200',
        'name' => 'Pref Profile User',
        'email' => 'pref-profile@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($userModel)
        ->put('/profile', [
            'name' => 'Pref Profile User',
            'notification_preferences' => [
                'info' => ['email' => '0'],
                'warning' => ['email' => '1'],
                'error' => ['email' => '1'],
            ],
        ])
        ->assertRedirect('/profile');

    expect(NotificationPreferenceModel::where('user_id', $userModel->id)->count())->toBe(3);

    $warningPref = NotificationPreferenceModel::where('user_id', $userModel->id)
        ->where('level', 'warning')
        ->first();
    expect($warningPref->channels)->toContain('email')
        ->and($warningPref->channels)->toContain('in_app');

    $infoPref = NotificationPreferenceModel::where('user_id', $userModel->id)
        ->where('level', 'info')
        ->first();
    expect($infoPref->channels)->toBe(['in_app']);
});

it('skips notification preferences when not submitted', function (): void {
    $userModel = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d201',
        'name' => 'No Pref User',
        'email' => 'no-pref@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($userModel)
        ->put('/profile', [
            'name' => 'No Pref User',
        ])
        ->assertRedirect('/profile');

    expect(NotificationPreferenceModel::where('user_id', $userModel->id)->count())->toBe(0);
});
