<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\DirectEmailSender;
use App\Domain\User\Contract\ValueObject\UserId;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

final readonly class LaravelDirectEmailSender implements DirectEmailSender
{
    public function __construct(
        private UserRepository $userRepository,
        private Mailer $mailer,
    ) {}

    public function sendToUser(string $userId, string $subject, string $body): void
    {
        $user = $this->userRepository->findById(new UserId($userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($userId);
        }

        $email = $user->email->value;

        $this->mailer->raw($body, static function (Message $message) use ($email, $subject): void {
            $message->to($email);
            $message->subject($subject);
        });
    }
}
