<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Tenancy;

use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use App\Domain\Tenancy\Exception\InvalidMailTransportException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidMailTransportExceptionTest extends TestCase
{
    #[Test]
    public function it_exposes_user_message_via_translator(): void
    {
        $invalidMailTransportException = new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_host');
        $translator = new class implements Translator
        {
            public function translate(string $key, array $params = []): string
            {
                return $key === 'messages.exceptions.invalid_mail_transport_host' ? 'translated' : $key;
            }

            public function locale(): string
            {
                return 'en';
            }
        };

        self::assertSame('translated', $invalidMailTransportException->userMessage($translator));
    }

    #[Test]
    public function it_maps_to_unprocessable_entity(): void
    {
        $invalidMailTransportException = new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_port');

        self::assertSame(HttpStatus::UNPROCESSABLE_ENTITY, $invalidMailTransportException->statusCode());
    }
}
