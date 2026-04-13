<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Service\NotificationChannelSender;
use App\Infrastructure\Notification\ChannelSenderRegistry;
use App\Infrastructure\Notification\Exception\UnsupportedNotificationChannelException;

it('returns sender for registered channel', function (): void {
    $emailSender = new class implements NotificationChannelSender
    {
        public function send(string $recipientId, string $recipientEmail, string $type, string $title, string $body, string $level, ?string $link): void {}

        public function supports(): string
        {
            return 'email';
        }
    };

    $registry = new ChannelSenderRegistry(['email' => $emailSender]);

    expect($registry->senderFor('email'))->toBe($emailSender);
});

it('throws for unregistered channel', function (): void {
    $registry = new ChannelSenderRegistry([]);

    $registry->senderFor('push');
})->throws(UnsupportedNotificationChannelException::class, 'No notification channel sender registered for channel [push].');
