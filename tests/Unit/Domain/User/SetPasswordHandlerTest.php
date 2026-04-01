<?php

declare(strict_types=1);

use App\Contract\Auth\PasswordManager;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Command\SetPassword\SetPasswordHandler;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserName;
use Tests\Helper\FakeUserRepository;

it('sets the password for an existing user', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);

    /** @var list<array{userId: string, rawPassword: string}> $setPasswordCalls */
    $setPasswordCalls = [];
    $passwordManager = new class($setPasswordCalls) implements PasswordManager
    {
        /** @param list<array{userId: string, rawPassword: string}> $calls */
        public function __construct(public array &$calls) {}

        public function setPassword(string $userId, string $rawPassword): void
        {
            $this->calls[] = ['userId' => $userId, 'rawPassword' => $rawPassword];
        }
    };

    $handler = new SetPasswordHandler($repository, $passwordManager);

    $handler->handle(new SetPasswordCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'new-password-123',
    ));

    expect($setPasswordCalls)->toHaveCount(1)
        ->and($setPasswordCalls[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($setPasswordCalls[0]['rawPassword'])->toBe('new-password-123');
});

it('throws when user not found', function (): void {
    $repository = new FakeUserRepository;
    $passwordManager = new class implements PasswordManager
    {
        public function setPassword(string $userId, string $rawPassword): void {}
    };

    $handler = new SetPasswordHandler($repository, $passwordManager);

    $handler->handle(new SetPasswordCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'password',
    ));
})->throws(UserNotFoundException::class);
