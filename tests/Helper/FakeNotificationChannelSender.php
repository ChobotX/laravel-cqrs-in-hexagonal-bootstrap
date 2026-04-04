<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Notification\Contract\Service\NotificationChannelSender;

final class FakeNotificationChannelSender implements NotificationChannelSender
{
    /** @var list<array{recipientId: string, type: string, title: string, body: string, level: string, link: ?string}> */
    public array $sent = [];

    public function __construct(
        private readonly string $channel = 'email',
    ) {}

    public function send(string $recipientId, string $type, string $title, string $body, string $level, ?string $link): void
    {
        $this->sent[] = ['recipientId' => $recipientId, 'type' => $type, 'title' => $title, 'body' => $body, 'level' => $level, 'link' => $link];
    }

    public function supports(): string
    {
        return $this->channel;
    }
}
