<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\PasswordPreviouslyUsedException;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('throws when new password matches the current hash', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'name' => 'Pwd User',
        'email' => 'pwd-user@test.com',
        'password' => Hash::make('same-password'),
    ]);

    $passwordManager = app(PasswordManager::class);

    expect(fn () => $passwordManager->setPassword($user->id, 'same-password'))
        ->toThrow(PasswordPreviouslyUsedException::class);
});

it('does nothing when the user id does not exist', function (): void {
    app(PasswordManager::class)->setPassword('00000000-0000-4000-8000-000000000099', 'any-password');

    expect(DB::connection('tenant')->table('user_password_history')->count())->toBe(0);
});

it('updates password and password_changed_at for a new password', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f21',
        'name' => 'Pwd Change',
        'email' => 'pwd-change@test.com',
        'password' => Hash::make('old-secret'),
        'password_changed_at' => now()->subDays(10),
    ]);

    $previousChangedAt = $user->password_changed_at;

    app(PasswordManager::class)->setPassword($user->id, 'new-secret-value');

    $user->refresh();

    expect(Hash::check('new-secret-value', $user->password))->toBeTrue()
        ->and($user->password_changed_at)->not->toEqual($previousChangedAt);
});

it('throws when new password matches a hash kept in password history', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f22',
        'name' => 'Pwd History',
        'email' => 'pwd-history@test.com',
        'password' => Hash::make('first-secret'),
    ]);

    $passwordManager = app(PasswordManager::class);
    $passwordManager->setPassword($user->id, 'second-secret');

    expect(fn () => $passwordManager->setPassword($user->id, 'first-secret'))
        ->toThrow(PasswordPreviouslyUsedException::class);
});
