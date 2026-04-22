<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\SendUserInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeTranslator;

function fakeInviteLinkGenerator(string $url = 'https://app.test/invite/abc123'): InviteLinkGenerator
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

function sendUserInviteUserBus(?User $user): FakeQueryBus
{
    return new FakeQueryBus([
        GetUserByIdQuery::class => fn (GetUserByIdQuery $getUserByIdQuery): User => $user
            ?? throw new UserNotFoundException($getUserByIdQuery->id),
    ]);
}

it('generates an invite link for the user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    /** @var list<string> $generatedForUserIds */
    $generatedForUserIds = [];
    $inviteLinkGenerator = new class($generatedForUserIds) implements InviteLinkGenerator
    {
        /** @param list<string> $calls */
        public function __construct(public array &$calls) {}

        public function generate(string $userId): string
        {
            $this->calls[] = $userId;

            return 'https://app.test/invite/abc123';
        }
    };

    $handler = new SendUserInviteHandler(
        new FakeCommandBus,
        sendUserInviteUserBus($user),
        $inviteLinkGenerator,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($generatedForUserIds)->toHaveCount(1)
        ->and($generatedForUserIds[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('dispatches a templated email command with the invite link', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $commandBus = new FakeCommandBus;

    $handler = new SendUserInviteHandler(
        $commandBus,
        sendUserInviteUserBus($user),
        fakeInviteLinkGenerator('https://app.test/invite/abc123'),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($commandBus->dispatched)->toHaveCount(1)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SendTemplatedEmailCommand::class);
    $sendTemplatedEmailCommand = $commandBus->dispatched[0];
    assert($sendTemplatedEmailCommand instanceof SendTemplatedEmailCommand);
    expect($sendTemplatedEmailCommand->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($sendTemplatedEmailCommand->templateType)->toBe('user_invite')
        ->and($sendTemplatedEmailCommand->locale)->toBe('en')
        ->and($sendTemplatedEmailCommand->variables)->toBe(['userName' => 'John Doe', 'link' => 'https://app.test/invite/abc123']);
});

it('collects a UserInviteSent event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $eventCollector = new FakeEventCollector;

    $handler = new SendUserInviteHandler(
        new FakeCommandBus,
        sendUserInviteUserBus($user),
        fakeInviteLinkGenerator(),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

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
    $handler = new SendUserInviteHandler(
        new FakeCommandBus,
        sendUserInviteUserBus(null),
        fakeInviteLinkGenerator(),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('does not dispatch send email command when user is not found', function (): void {
    $commandBus = new FakeCommandBus;
    $eventCollector = new FakeEventCollector;

    $handler = new SendUserInviteHandler(
        $commandBus,
        sendUserInviteUserBus(null),
        fakeInviteLinkGenerator(),
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserNotFoundException) {
    }

    expect($commandBus->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
