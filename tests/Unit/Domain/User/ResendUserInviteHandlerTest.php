<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\ResendUserInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeTranslator;

function resendFakeInviteLinkGenerator(string $url = 'https://app.test/invite/abc123'): InviteLinkGenerator
{
    return new readonly class($url) implements InviteLinkGenerator
    {
        public function __construct(private string $url) {}

        public function generate(string $userId): string
        {
            return $this->url;
        }
    };
}

function resendInviteUserBus(?User $user): FakeQueryBus
{
    return new FakeQueryBus([
        GetUserByIdQuery::class => fn (GetUserByIdQuery $query): User => $user
            ?? throw new UserNotFoundException($query->id),
    ]);
}

it('sends an invite email to a non-activated user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $commandBus = new FakeCommandBus;

    $handler = new ResendUserInviteHandler(
        $commandBus,
        resendInviteUserBus($user),
        resendFakeInviteLinkGenerator('https://app.test/invite/resend456'),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($commandBus->dispatched)->toHaveCount(1)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SendTemplatedEmailCommand::class);
    $sendTemplatedEmailCommand = $commandBus->dispatched[0];
    assert($sendTemplatedEmailCommand instanceof SendTemplatedEmailCommand);
    expect($sendTemplatedEmailCommand->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($sendTemplatedEmailCommand->templateType)->toBe('user_invite')
        ->and($sendTemplatedEmailCommand->locale)->toBe('en')
        ->and($sendTemplatedEmailCommand->variables)->toBe(['userName' => 'John Doe', 'link' => 'https://app.test/invite/resend456']);
});

it('collects a UserInviteSent event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        new FakeCommandBus,
        resendInviteUserBus($user),
        resendFakeInviteLinkGenerator(),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(UserInviteSent::class);
    assert($eventCollector->collected[0] instanceof UserInviteSent);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->userName)->toBe('John Doe')
        ->and($eventCollector->collected[0]->inviteLink)->toBe('https://app.test/invite/abc123')
        ->and($eventCollector->collected[0]->locale)->toBe('en')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $handler = new ResendUserInviteHandler(
        new FakeCommandBus,
        resendInviteUserBus(null),
        resendFakeInviteLinkGenerator(),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws UserAlreadyActivatedException when user is activated', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        isActivated: true,
    );

    $handler = new ResendUserInviteHandler(
        new FakeCommandBus,
        resendInviteUserBus($user),
        resendFakeInviteLinkGenerator(),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserAlreadyActivatedException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] has already been activated.');

it('does not send email when user is not found', function (): void {
    $commandBus = new FakeCommandBus;
    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        $commandBus,
        resendInviteUserBus(null),
        resendFakeInviteLinkGenerator(),
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserNotFoundException) {
    }

    expect($commandBus->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('does not send email when user is already activated', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        isActivated: true,
    );

    $commandBus = new FakeCommandBus;
    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        $commandBus,
        resendInviteUserBus($user),
        resendFakeInviteLinkGenerator(),
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserAlreadyActivatedException) {
    }

    expect($commandBus->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
