<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserMapper;
use App\Infrastructure\Eloquent\User\UserModel;
use Carbon\CarbonImmutable;

it('maps two-factor fields from eloquent model to domain user', function (): void {
    $model = new UserModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440901';
    $model->name = 'Mapper User';
    $model->email = 'mapper@example.com';
    $model->password = 'hash';
    $model->password_changed_at = CarbonImmutable::now()->subDay();
    $model->email_two_factor_enabled = true;
    $model->email_two_factor_confirmed_at = CarbonImmutable::now()->subHour();
    $model->totp_secret = 'SECRET';
    $model->totp_confirmed_at = CarbonImmutable::now();

    $user = (new UserMapper)->toDomain($model);

    expect($user->emailTwoFactorEnabled)->toBeTrue()
        ->and($user->emailTwoFactorConfirmedAt)->not->toBeNull()
        ->and($user->totpSecret)->toBe('SECRET')
        ->and($user->totpConfirmedAt)->not->toBeNull();
});
