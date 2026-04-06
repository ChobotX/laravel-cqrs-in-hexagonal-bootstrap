<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Service\DirectEmailSender;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\RequestPasswordResetHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTranslator;
use Tests\Helper\FakeUserRepository;

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

/**
 * @param  list<array{userId: string, subject: string, body: string}>  $calls
 */
function resetFakeDirectEmailSender(array &$calls = []): DirectEmailSender
{
    return new class($calls) implements DirectEmailSender
    {
        /** @param list<array{userId: string, subject: string, body: string}> $calls */
        public function __construct(public array &$calls) {}

        public function sendToUser(string $userId, string $subject, string $body): void
        {
            $this->calls[] = ['userId' => $userId, 'subject' => $subject, 'body' => $body];
        }
    };
}

it('sends a password reset email to the user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $sendCalls = [];
    $translator = new FakeTranslator;

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker('https://app.test/reset/abc123'),
        $repository,
        resetFakeDirectEmailSender($sendCalls),
        new FakeEventCollector,
        $translator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($sendCalls)->toHaveCount(1)
        ->and($sendCalls[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($sendCalls[0]['subject'])->toBe('messages.email.password_reset_subject');

    expect($translator->calls)->toContainEqual([
        'key' => 'messages.email.password_reset_body',
        'params' => ['link' => 'https://app.test/reset/abc123'],
    ]);
});

it('collects a PasswordResetRequested event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;
    $sendCalls = [];

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker(),
        $repository,
        resetFakeDirectEmailSender($sendCalls),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PasswordResetRequested::class);
    assert($eventCollector->collected[0] instanceof PasswordResetRequested);
    expect($eventCollector->collected[0]->email)->toBe('john@example.com')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('does nothing when broker returns null', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;
    $sendCalls = [];

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker(null),
        $repository,
        resetFakeDirectEmailSender($sendCalls),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'john@example.com'));

    expect($sendCalls)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('does nothing when user is not found in repository', function (): void {
    $eventCollector = new FakeEventCollector;
    $sendCalls = [];

    $handler = new RequestPasswordResetHandler(
        resetFakePasswordResetBroker('https://app.test/reset/abc123'),
        new FakeUserRepository,
        resetFakeDirectEmailSender($sendCalls),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new RequestPasswordResetCommand(email: 'unknown@example.com'));

    expect($sendCalls)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
