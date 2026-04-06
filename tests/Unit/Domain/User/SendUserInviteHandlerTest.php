<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Service\DirectEmailSender;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\SendUserInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
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

/**
 * @param  list<array{userId: string, subject: string, body: string}>  $calls
 */
function fakeDirectEmailSender(array &$calls = []): DirectEmailSender
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

    $sendCalls = [];
    $handler = new SendUserInviteHandler(
        $repository,
        $inviteLinkGenerator,
        fakeDirectEmailSender($sendCalls),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($generatedForUserIds)->toHaveCount(1)
        ->and($generatedForUserIds[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('sends an email with the invite link', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $sendCalls = [];
    $translator = new FakeTranslator;

    $handler = new SendUserInviteHandler(
        $repository,
        fakeInviteLinkGenerator('https://app.test/invite/abc123'),
        fakeDirectEmailSender($sendCalls),
        new FakeEventCollector,
        $translator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($sendCalls)->toHaveCount(1)
        ->and($sendCalls[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($sendCalls[0]['subject'])->toBe('messages.email.invite_subject');

    expect($translator->calls)->toContainEqual([
        'key' => 'messages.email.invite_body',
        'params' => ['link' => 'https://app.test/invite/abc123'],
    ]);
});

it('collects a UserInviteSent event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;
    $sendCalls = [];

    $handler = new SendUserInviteHandler(
        $repository,
        fakeInviteLinkGenerator(),
        fakeDirectEmailSender($sendCalls),
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(UserInviteSent::class);
    assert($eventCollector->collected[0] instanceof UserInviteSent);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $sendCalls = [];

    $handler = new SendUserInviteHandler(
        new FakeUserRepository,
        fakeInviteLinkGenerator(),
        fakeDirectEmailSender($sendCalls),
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('does not send email when user is not found', function (): void {
    $sendCalls = [];
    $eventCollector = new FakeEventCollector;

    $handler = new SendUserInviteHandler(
        new FakeUserRepository,
        fakeInviteLinkGenerator(),
        fakeDirectEmailSender($sendCalls),
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new SendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserNotFoundException) {
    }

    expect($sendCalls)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
