<?php

declare(strict_types=1);

use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationPreferenceRepository;
use App\Infrastructure\Eloquent\Notification\NotificationPreferenceMapper;
use App\Infrastructure\Eloquent\Notification\NotificationPreferenceModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createPrefRepoUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544b000',
        'name' => 'Pref Repo User',
        'email' => 'pref-repo@test.com',
        'password' => Hash::make('password'),
    ]);
}

function makePrefRepo(): EloquentNotificationPreferenceRepository
{
    return new EloquentNotificationPreferenceRepository(new NotificationPreferenceMapper);
}

it('returns null when no preferences stored', function (): void {
    createPrefRepoUser();
    $eloquentNotificationPreferenceRepository = makePrefRepo();

    expect($eloquentNotificationPreferenceRepository->findByUserId('550e8400-e29b-41d4-a716-44665544b000'))->toBeNull();
});

it('saves and retrieves preferences', function (): void {
    $userModel = createPrefRepoUser();
    $eloquentNotificationPreferenceRepository = makePrefRepo();

    $preferences = new NotificationPreferences(
        userId: $userModel->id,
        preferences: [
            new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp]),
            new ChannelPreference(NotificationLevel::Error, [NotificationChannel::InApp, NotificationChannel::Email]),
        ],
    );

    $eloquentNotificationPreferenceRepository->save($preferences);

    $found = $eloquentNotificationPreferenceRepository->findByUserId($userModel->id);
    expect($found)->not->toBeNull()
        ->and($found->userId)->toBe($userModel->id)
        ->and($found->preferences)->toHaveCount(2)
        ->and($found->preferences[0]->level)->toBe(NotificationLevel::Info)
        ->and($found->preferences[1]->channels)->toContain(NotificationChannel::Email);
});

it('replaces existing preferences on save', function (): void {
    $userModel = createPrefRepoUser();
    $eloquentNotificationPreferenceRepository = makePrefRepo();

    $eloquentNotificationPreferenceRepository->save(new NotificationPreferences(
        userId: $userModel->id,
        preferences: [
            new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp]),
            new ChannelPreference(NotificationLevel::Warning, [NotificationChannel::InApp]),
        ],
    ));

    $eloquentNotificationPreferenceRepository->save(new NotificationPreferences(
        userId: $userModel->id,
        preferences: [
            new ChannelPreference(NotificationLevel::Error, [NotificationChannel::InApp, NotificationChannel::Email]),
        ],
    ));

    $found = $eloquentNotificationPreferenceRepository->findByUserId($userModel->id);
    expect($found->preferences)->toHaveCount(1)
        ->and($found->preferences[0]->level)->toBe(NotificationLevel::Error);

    expect(NotificationPreferenceModel::where('user_id', $userModel->id)->count())->toBe(1);
});
