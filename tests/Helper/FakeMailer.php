<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Mail\SentMessage;
use RuntimeException;

final class FakeMailer implements Mailer
{
    /** @var list<array{to: string, subject: string, body: string}> */
    public array $sent = [];

    public function raw($text, $callback): ?SentMessage
    {
        $message = new \Illuminate\Mail\Message(new \Symfony\Component\Mime\Email);
        assert(is_callable($callback));
        $callback($message);
        $subject = $message->getSymfonyMessage()->getSubject();
        $this->sent[] = [
            'to' => $message->getSymfonyMessage()->getTo()[0]->getAddress(),
            'subject' => $subject ?? '',
            'body' => $text,
        ];

        return null;
    }

    public function to($users): PendingMail
    {
        throw new RuntimeException('Not expected');
    }

    public function bcc($users): PendingMail
    {
        throw new RuntimeException('Not expected');
    }

    public function send($view, array $data = [], $callback = null): ?SentMessage
    {
        if (is_callable($callback)) {
            $message = new \Illuminate\Mail\Message(new \Symfony\Component\Mime\Email);
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

    public function sendNow($mailable, ?array $data = [], $callback = null): ?SentMessage
    {
        return null;
    }
}
