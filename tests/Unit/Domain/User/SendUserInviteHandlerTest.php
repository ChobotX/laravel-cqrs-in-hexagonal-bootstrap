<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\SendUserInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTemplatedEmailDispatcher;
use Tests\Helper\FakeTranslator;
use Tests\Helper\FakeUserRepository;

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

it('generates an invite link for the user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);

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
        $repository,
        $inviteLinkGenerator,
        new FakeTemplatedEmailDispatcher,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($generatedForUserIds)->toHaveCount(1)
        ->and($generatedForUserIds[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('dispatches a templated email with the invite link', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $emailDispatcher = new FakeTemplatedEmailDispatcher;

    $handler = new SendUserInviteHandler(
        $repository,
        fakeInviteLinkGenerator('https://app.test/invite/abc123'),
        $emailDispatcher,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($emailDispatcher->dispatched)->toHaveCount(1)
        ->and($emailDispatcher->dispatched[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($emailDispatcher->dispatched[0]['templateType'])->toBe('user_invite')
        ->and($emailDispatcher->dispatched[0]['locale'])->toBe('en')
        ->and($emailDispatcher->dispatched[0]['variables'])->toBe(['userName' => 'John Doe', 'link' => 'https://app.test/invite/abc123']);
});

it('collects a UserInviteSent event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;

    $handler = new SendUserInviteHandler(
        $repository,
        fakeInviteLinkGenerator(),
        new FakeTemplatedEmailDispatcher,
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(UserInviteSent::class);
    assert($eventCollector->collected[0] instanceof UserInviteSent);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->userName)->toBe('John Doe')
        ->and($eventCollector->collected[0]->inviteLink)->toBe('https://app.test/invite/abc123')
        ->and($eventCollector->collected[0]->locale)->toBe('en')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $handler = new SendUserInviteHandler(
        new FakeUserRepository,
        fakeInviteLinkGenerator(),
        new FakeTemplatedEmailDispatcher,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('does not send email when user is not found', function (): void {
    $emailDispatcher = new FakeTemplatedEmailDispatcher;
    $eventCollector = new FakeEventCollector;

    $handler = new SendUserInviteHandler(
        new FakeUserRepository,
        fakeInviteLinkGenerator(),
        $emailDispatcher,
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserNotFoundException) {
    }

    expect($emailDispatcher->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
