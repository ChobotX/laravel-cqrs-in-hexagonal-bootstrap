<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\ResetPasswordCommand;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\PasswordResetCompleted;
use App\Domain\User\Contract\Exception\InvalidPasswordResetTokenException;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Domain\User\Handler\Command\ResetPasswordHandler;
use Tests\Helper\FakeEventCollector;

function completeFakePasswordResetBroker(string $userId = '550e8400-e29b-41d4-a716-446655440000'): PasswordResetBroker
{
    return new readonly class($userId) implements PasswordResetBroker
    {
        public function __construct(private string $userId) {}

        public function createResetLink(string $email): ?string
        {
            throw new RuntimeException('Not expected to be called');
        }

        public function reset(string $email, string $token, string $newPassword): string
        {
            return $this->userId;
        }
    };
}

function throwingFakePasswordResetBroker(): PasswordResetBroker
{
    return new class implements PasswordResetBroker
    {
        public function createResetLink(string $email): ?string
        {
            throw new RuntimeException('Not expected to be called');
        }

        public function reset(string $email, string $token, string $newPassword): string
        {
            throw new InvalidPasswordResetTokenException;
        }
    };
}

it('resets the password and collects PasswordResetCompleted and PasswordChanged events', function (): void {
    $eventCollector = new FakeEventCollector;

    $handler = new ResetPasswordHandler(
        completeFakePasswordResetBroker('550e8400-e29b-41d4-a716-446655440000'),
        $eventCollector,
    );

    $handler->handle(new ResetPasswordCommand(
        email: 'john@example.com',
        token: 'valid-token',
        rawPassword: 'NewSecureP@ss1!',
    ));

    expect($eventCollector->collected)->toHaveCount(2);

    expect($eventCollector->collected[0])->toBeInstanceOf(PasswordResetCompleted::class);
    assert($eventCollector->collected[0] instanceof PasswordResetCompleted);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);

    expect($eventCollector->collected[1])->toBeInstanceOf(PasswordChanged::class);
    assert($eventCollector->collected[1] instanceof PasswordChanged);
    expect($eventCollector->collected[1]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[1]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws InvalidPasswordResetTokenException when token is invalid', function (): void {
    $handler = new ResetPasswordHandler(
        throwingFakePasswordResetBroker(),
        new FakeEventCollector,
    );

    $handler->handle(new ResetPasswordCommand(
        email: 'john@example.com',
        token: 'invalid-token',
        rawPassword: 'NewSecureP@ss1!',
    ));
})->throws(InvalidPasswordResetTokenException::class, 'Invalid or expired password reset token.');

it('does not collect events when token is invalid', function (): void {
    $eventCollector = new FakeEventCollector;

    $handler = new ResetPasswordHandler(
        throwingFakePasswordResetBroker(),
        $eventCollector,
    );

    try {
        $handler->handle(new ResetPasswordCommand(
            email: 'john@example.com',
            token: 'invalid-token',
            rawPassword: 'NewSecureP@ss1!',
        ));
    } catch (InvalidPasswordResetTokenException) {
    }

    expect($eventCollector->collected)->toHaveCount(0);
});
