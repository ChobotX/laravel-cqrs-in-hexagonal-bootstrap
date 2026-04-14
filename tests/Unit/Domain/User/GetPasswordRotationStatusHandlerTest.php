<?php

declare(strict_types=1);

use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetPasswordRotationStatusQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Query\GetPasswordRotationStatusHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakePasswordRotationSettingsRepository;
use Tests\Helper\FakeUserRepository;

/**
 * @param  non-empty-string  $userId
 */
function statusHandlerAuthenticatedUser(?string $userId): AuthenticatedUser
{
    return new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
        }

        public function name(): ?string
        {
            return null;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };
}

it('returns ok when rotation is disabled', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: false, maxAgeDays: null, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $userRepository = new FakeUserRepository;
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::OK);
});

it('returns ok when authenticated user id is missing', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: true, maxAgeDays: 30, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $userRepository = new FakeUserRepository;
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser(null),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::OK);
});

it('returns expired when password never changed', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: true, maxAgeDays: 30, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        passwordChangedAt: null,
    );
    $userRepository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::EXPIRED);
});

it('returns expired when password age exceeds policy', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: true, maxAgeDays: 30, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        passwordChangedAt: new DateTimeImmutable('-200 days'),
    );
    $userRepository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::EXPIRED);
});

it('returns warning in the final window before expiry', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: true, maxAgeDays: 100, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        passwordChangedAt: new DateTimeImmutable('-91 days'),
    );
    $userRepository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::WARNING);
});

it('returns ok when password is fresh within policy', function (): void {
    $policy = new PasswordRotationSettings(rotationEnabled: true, maxAgeDays: 100, historyCount: 5);
    $repository = new FakePasswordRotationSettingsRepository($policy);
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        passwordChangedAt: new DateTimeImmutable('-5 days'),
    );
    $userRepository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new GetPasswordRotationStatusHandler(
        $repository,
        $userRepository,
        statusHandlerAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $passwordRotationUiStatus = $handler->handle(new GetPasswordRotationStatusQuery);

    expect($passwordRotationUiStatus->value)->toBe(PasswordRotationUiStatus::OK);
});
