<?php

declare(strict_types=1);

use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\User;
use App\Domain\User\UserName;
use App\Infrastructure\Notification\EmailNotificationSender;
use Tests\Helper\FakeMailer;
use Tests\Helper\FakeQueryBus;

it('sends email to resolved user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => $user,
    ]);

    $mailer = new FakeMailer;

    $sender = new EmailNotificationSender($queryBus, $mailer);

    $sender->send('550e8400-e29b-41d4-a716-446655440000', 'user.welcome', 'Welcome!', 'Body text', 'info', null);

    expect($mailer->sent)->toHaveCount(1)
        ->and($mailer->sent[0]['to'])->toBe('john@example.com')
        ->and($mailer->sent[0]['subject'])->toBe('Welcome!');
});

it('supports email channel', function (): void {
    $queryBus = new FakeQueryBus;
    $mailer = new FakeMailer;

    $sender = new EmailNotificationSender($queryBus, $mailer);

    expect($sender->supports())->toBe('email');
});
