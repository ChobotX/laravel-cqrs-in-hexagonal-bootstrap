<?php

declare(strict_types=1);

namespace Tests\Helper;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Mail\PendingMail;
use Illuminate\Mail\SentMessage;
use RuntimeException;
use Symfony\Component\Mime\Email;

final class FakeMailer implements Mailer
{
    /** @var list<array{to: string, subject: string, body: string}> */
    public array $sent = [];

    public function raw($text, $callback): ?SentMessage
    {
        if (! is_callable($callback)) {
            return null;
        }

        $message = new Message(new Email);
        $callback($message);
        $subject = $message->getSymfonyMessage()->getSubject();
        $this->sent[] = [
            'to' => $message->getSymfonyMessage()->getTo()[0]->getAddress(),
            'subject' => $subject ?? '',
            'body' => $text,
        ];

        return null;
    }

    public function to(mixed $users): PendingMail
    {
        throw new RuntimeException('Not expected');
    }

    public function bcc(mixed $users): PendingMail
    {
        throw new RuntimeException('Not expected');
    }

    /**
     * @param  Mailable|string|array<int|string, mixed>  $view
     * @param  array<mixed>  $data
     * @param  Closure|string|null  $callback
     */
    public function send($view, array $data = [], $callback = null): ?SentMessage
    {
        if (is_callable($callback)) {
            $message = new Message(new Email);
            $callback($message);
            $symfony = $message->getSymfonyMessage();
            $this->sent[] = [
                'to' => $symfony->getTo()[0]->getAddress(),
                'subject' => $symfony->getSubject() ?? '',
                'body' => (string) ($symfony->getHtmlBody() ?? ''),
            ];
        }

        return null;
    }

    /**
     * @param  Mailable|string|array<int|string, mixed>  $mailable
     * @param  array<mixed>  $data
     * @param  Closure|string|null  $callback
     */
    public function sendNow($mailable, array $data = [], $callback = null): ?SentMessage
    {
        return null;
    }
}
