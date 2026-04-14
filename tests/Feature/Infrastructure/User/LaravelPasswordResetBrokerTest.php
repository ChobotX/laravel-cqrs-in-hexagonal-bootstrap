<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\InvalidPasswordResetTokenException;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Infrastructure\User\LaravelPasswordResetBroker;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

function resetBroker(): LaravelPasswordResetBroker
{
    return new LaravelPasswordResetBroker(
        app(PasswordBrokerManager::class),
        app(UserProvider::class),
        app(UrlGenerator::class),
        app(PasswordManager::class),
    );
}

it('creates a reset link for existing user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440770',
        'name' => 'Link User',
        'email' => 'link-user@example.com',
        'password' => Hash::make('password123'),
    ]);

    $link = resetBroker()->createResetLink('link-user@example.com');

    expect($link)->not->toBeNull()
        ->and($link)->toContain('/reset-password/')
        ->and($link)->toContain('email=');
});

it('returns null for non-existent user', function (): void {
    $link = resetBroker()->createResetLink('nonexistent@example.com');

    expect($link)->toBeNull();
});

it('resets password with valid token', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440771',
        'name' => 'Reset Valid',
        'email' => 'reset-valid@example.com',
        'password' => Hash::make('oldpassword1'),
    ]);

    $token = Password::broker()->createToken($user);

    $userId = resetBroker()->reset('reset-valid@example.com', $token, 'newpassword1');

    expect($userId)->toBe('550e8400-e29b-41d4-a716-446655440771');

    $updated = UserModel::find('550e8400-e29b-41d4-a716-446655440771');
    expect(Hash::check('newpassword1', $updated->password))->toBeTrue();
});

it('throws InvalidPasswordResetTokenException for invalid token', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440772',
        'name' => 'Invalid Token',
        'email' => 'invalid-token@example.com',
        'password' => Hash::make('password123'),
    ]);

    resetBroker()->reset('invalid-token@example.com', 'bad-token', 'newpassword1');
})->throws(InvalidPasswordResetTokenException::class);

it('throws InvalidPasswordResetTokenException for non-existent email', function (): void {
    resetBroker()->reset('no-such-user@example.com', 'any-token', 'newpassword1');
})->throws(InvalidPasswordResetTokenException::class);

it('throws when user lookup fails after successful reset', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440773',
        'name' => 'Vanishing User',
        'email' => 'vanish@example.com',
        'password' => Hash::make('oldpassword1'),
    ]);

    $token = Password::broker()->createToken($user);

    $mock = Mockery::mock(UserProvider::class);
    $mock->shouldReceive('retrieveByCredentials')->andReturnNull();

    $broker = new LaravelPasswordResetBroker(
        app(PasswordBrokerManager::class),
        $mock,
        app(UrlGenerator::class),
        app(PasswordManager::class),
    );

    $broker->reset('vanish@example.com', $token, 'newpassword1');
})->throws(InvalidPasswordResetTokenException::class);
