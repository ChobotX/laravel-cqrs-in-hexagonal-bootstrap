<?php

declare(strict_types=1);

use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Infrastructure\User\EloquentUserTwoFactorStateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('returns default state for missing user', function (): void {
    $repository = new EloquentUserTwoFactorStateRepository;
    $userTwoFactorState = $repository->get(new UserId('550e8400-e29b-41d4-a716-446655440902'));

    expect($userTwoFactorState->emailEnabled)->toBeFalse()
        ->and($userTwoFactorState->totpSecret)->toBeNull();
});

it('persists and reads user two-factor state', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440903',
        'name' => 'State User',
        'email' => 'state-user@example.com',
        'password' => Hash::make('password'),
    ]);

    $repository = new EloquentUserTwoFactorStateRepository;
    $repository->save(
        new UserId('550e8400-e29b-41d4-a716-446655440903'),
        new UserTwoFactorState(true, new DateTimeImmutable('-1 minute'), 'secret', new DateTimeImmutable),
    );

    $userTwoFactorState = $repository->get(new UserId('550e8400-e29b-41d4-a716-446655440903'));

    expect($userTwoFactorState->emailEnabled)->toBeTrue()
        ->and($userTwoFactorState->emailConfirmedAt)->not->toBeNull()
        ->and($userTwoFactorState->totpSecret)->toBe('secret')
        ->and($userTwoFactorState->totpConfirmedAt)->not->toBeNull();
});

it('persists and reads totp recovery code hashes', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440905',
        'name' => 'Recovery Hash User',
        'email' => 'recovery-hash-user@example.com',
        'password' => Hash::make('password'),
    ]);

    $repository = new EloquentUserTwoFactorStateRepository;
    $hashes = ['hash-one', 'hash-two'];
    $repository->save(
        new UserId('550e8400-e29b-41d4-a716-446655440905'),
        new UserTwoFactorState(false, null, 'secret', null, $hashes),
    );

    $userTwoFactorState = $repository->get(new UserId('550e8400-e29b-41d4-a716-446655440905'));

    expect($userTwoFactorState->totpRecoveryCodeHashes)->toBe($hashes);

    DB::connection('tenant')->table('users')->where('id', '550e8400-e29b-41d4-a716-446655440905')->update([
        'totp_recovery_code_hashes' => json_encode(['hash-one', 2, 'hash-three']),
    ]);

    $filtered = $repository->get(new UserId('550e8400-e29b-41d4-a716-446655440905'));

    expect($filtered->totpRecoveryCodeHashes)->toBe(['hash-one', 'hash-three']);
});

it('persists nullable two-factor confirmation fields', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440904',
        'name' => 'State User Nullable',
        'email' => 'state-user-null@example.com',
        'password' => Hash::make('password'),
    ]);

    $repository = new EloquentUserTwoFactorStateRepository;
    $repository->save(
        new UserId('550e8400-e29b-41d4-a716-446655440904'),
        new UserTwoFactorState(false, null, null, null),
    );

    $userTwoFactorState = $repository->get(new UserId('550e8400-e29b-41d4-a716-446655440904'));
    expect($userTwoFactorState->emailEnabled)->toBeFalse()
        ->and($userTwoFactorState->emailConfirmedAt)->toBeNull()
        ->and($userTwoFactorState->totpSecret)->toBeNull()
        ->and($userTwoFactorState->totpConfirmedAt)->toBeNull();
});
