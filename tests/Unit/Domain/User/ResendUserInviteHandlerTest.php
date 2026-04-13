<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\ResendUserInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTemplatedEmailDispatcher;
use Tests\Helper\FakeTranslator;
use Tests\Helper\FakeUserRepository;

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

it('sends an invite email to a non-activated user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $emailDispatcher = new FakeTemplatedEmailDispatcher;

    $handler = new ResendUserInviteHandler(
        $repository,
        resendFakeInviteLinkGenerator('https://app.test/invite/resend456'),
        $emailDispatcher,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($emailDispatcher->dispatched)->toHaveCount(1)
        ->and($emailDispatcher->dispatched[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($emailDispatcher->dispatched[0]['templateType'])->toBe('user_invite')
        ->and($emailDispatcher->dispatched[0]['locale'])->toBe('en')
        ->and($emailDispatcher->dispatched[0]['variables'])->toBe(['userName' => 'John Doe', 'link' => 'https://app.test/invite/resend456']);
});

it('collects a UserInviteSent event', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        $repository,
        resendFakeInviteLinkGenerator(),
        new FakeTemplatedEmailDispatcher,
        $eventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(2)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TemplatedEmailSent::class)
        ->and($eventCollector->collected[1])->toBeInstanceOf(UserInviteSent::class);
    assert($eventCollector->collected[1] instanceof UserInviteSent);
    expect($eventCollector->collected[1]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[1]->userName)->toBe('John Doe')
        ->and($eventCollector->collected[1]->inviteLink)->toBe('https://app.test/invite/abc123')
        ->and($eventCollector->collected[1]->locale)->toBe('en')
        ->and($eventCollector->collected[1]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $handler = new ResendUserInviteHandler(
        new FakeUserRepository,
        resendFakeInviteLinkGenerator(),
        new FakeTemplatedEmailDispatcher,
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

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);

    $handler = new ResendUserInviteHandler(
        $repository,
        resendFakeInviteLinkGenerator(),
        new FakeTemplatedEmailDispatcher,
        new FakeEventCollector,
        new FakeTranslator,
    );

    $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserAlreadyActivatedException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] has already been activated.');

it('does not send email when user is not found', function (): void {
    $emailDispatcher = new FakeTemplatedEmailDispatcher;
    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        new FakeUserRepository,
        resendFakeInviteLinkGenerator(),
        $emailDispatcher,
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserNotFoundException) {
    }

    expect($emailDispatcher->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('does not send email when user is already activated', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        isActivated: true,
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $emailDispatcher = new FakeTemplatedEmailDispatcher;
    $eventCollector = new FakeEventCollector;

    $handler = new ResendUserInviteHandler(
        $repository,
        resendFakeInviteLinkGenerator(),
        $emailDispatcher,
        $eventCollector,
        new FakeTranslator,
    );

    try {
        $handler->handle(new ResendUserInviteCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
    } catch (UserAlreadyActivatedException) {
    }

    expect($emailDispatcher->dispatched)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
