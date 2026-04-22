<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Query\GetUserByEmailQuery;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\RequestPasswordResetHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeTranslator;

function resetFakePasswordResetBroker(?string $resetLink = 'https://app.test/reset/abc123'): PasswordResetBroker
{
    return new readonly class($resetLink) implements PasswordResetBroker
    {
        public function __construct(private ?string $resetLink) {}

        public function createResetLink(string $email): ?string
        {
            return $this->resetLink;
        }

        public function reset(string $email, string $token, string $newPassword): string
        {
            throw new RuntimeException('Not expected to be called');
        }
    };
}

function resetUserBus(?User $user): FakeQueryBus
{
    return new FakeQueryBus([
        GetUserByEmailQuery::class => $user,
    ]);
}

it('sends a password reset email to the user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $commandBus = new FakeCommandBus;

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker('https://app.test/reset/abc123'),
        $commandBus,
        resetUserBus($user),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($commandBus->dispatched)->toHaveCount(1)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SendTemplatedEmailCommand::class);
    $sendTemplatedEmailCommand = $commandBus->dispatched[0];
    assert($sendTemplatedEmailCommand instanceof SendTemplatedEmailCommand);
    expect($sendTemplatedEmailCommand->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($sendTemplatedEmailCommand->templateType)->toBe('password_reset')
        ->and($sendTemplatedEmailCommand->locale)->toBe('en')
        ->and($sendTemplatedEmailCommand->variables)->toBe(['link' => 'https://app.test/reset/abc123']);
});

it('collects a PasswordResetRequested event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $eventCollector = new FakeEventCollector;

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker(),
        new FakeCommandBus,
        resetUserBus($user),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(PasswordResetRequested::class);
    assert($eventCollector->collected[0] instanceof PasswordResetRequested);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->email)->toBe('john@example.com')
        ->and($eventCollector->collected[0]->resetLink)->toBe('https://app.test/reset/abc123')
        ->and($eventCollector->collected[0]->locale)->toBe('en')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('does nothing when broker returns null', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $commandBus = new FakeCommandBus;
    $eventCollector = new FakeEventCollector;

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker(null),
        $commandBus,
        resetUserBus($user),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($commandBus->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('does nothing when user is not found in repository', function (): void {
    $commandBus = new FakeCommandBus;
    $eventCollector = new FakeEventCollector;

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker('https://app.test/reset/abc123'),
        $commandBus,
        resetUserBus(null),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'unknown@example.com'));

    expect($commandBus->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
