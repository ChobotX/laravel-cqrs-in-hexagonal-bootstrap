<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Service\TwoFactorCodeNotifier;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

final readonly class LaravelTwoFactorCodeNotifier implements TwoFactorCodeNotifier
{
    public function send(string $email, string $subject, string $body): void
    {
        Mail::raw(
            $body,
            static fn (Message $message) => $message->to($email)->subject($subject),
        );
    }
}
