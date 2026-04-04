<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Bus\QueryBus;
use App\Domain\Notification\Contract\Service\NotificationChannelSender;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

final readonly class EmailNotificationSender implements NotificationChannelSender
{
    public function __construct(
        private QueryBus $queryBus,
        private Mailer $mailer,
    ) {}

    public function send(
        string $recipientId,
        string $type,
        string $title,
        string $body,
        string $level,
        ?string $link,
    ): void {
        /** @var User $user */
        $user = $this->queryBus->dispatch(new GetUserByIdQuery($recipientId));

        $this->mailer->raw($body, function (Message $message) use ($user, $title): void {
            $message->to($user->email->value);
            $message->subject($title);
        });
    }

    public function supports(): string
    {
        return 'email';
    }
}
