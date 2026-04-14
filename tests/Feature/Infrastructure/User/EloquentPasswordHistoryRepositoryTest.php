<?php

declare(strict_types=1);

use App\Domain\User\Contract\Repository\PasswordHistoryRepository;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('prune does nothing when the user has no history rows', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'name' => 'No History',
        'email' => 'no-history@test.com',
        'password' => Hash::make('password'),
    ]);

    app(PasswordHistoryRepository::class)->pruneToMaxEntries($user->id, 3);

    expect(DB::connection('tenant')->table('user_password_history')->where('user_id', $user->id)->count())->toBe(0);
});

it('appends lists recent hashes and prunes older entries', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f31',
        'name' => 'History User',
        'email' => 'history-user@test.com',
        'password' => Hash::make('password'),
    ]);

    $passwordHistoryRepository = app(PasswordHistoryRepository::class);
    $passwordHistoryRepository->append($user->id, 'hash-oldest', new DateTimeImmutable('2020-01-01T00:00:00+00:00'));
    $passwordHistoryRepository->append($user->id, 'hash-mid', new DateTimeImmutable('2020-02-01T00:00:00+00:00'));
    $passwordHistoryRepository->append($user->id, 'hash-newest', new DateTimeImmutable('2020-03-01T00:00:00+00:00'));

    $listed = $passwordHistoryRepository->listRecentHashes($user->id, 10);

    expect($listed)->toBe(['hash-newest', 'hash-mid', 'hash-oldest']);

    $passwordHistoryRepository->pruneToMaxEntries($user->id, 1);

    $remaining = DB::connection('tenant')->table('user_password_history')
        ->where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->pluck('password_hash')
        ->all();

    expect($remaining)->toBe(['hash-newest']);
});
