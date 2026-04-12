<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Tenancy;

use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use App\Domain\Tenancy\Exception\InvalidTenantDisplayTimezoneException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidTenantDisplayTimezoneExceptionTest extends TestCase
{
    #[Test]
    public function it_exposes_user_message_via_translator(): void
    {
        $invalidTenantDisplayTimezoneException = new InvalidTenantDisplayTimezoneException('Bad/Zone');
        $translator = new class implements Translator
        {
            public function translate(string $key, array $params = []): string
            {
                return $key === 'messages.exceptions.invalid_tenant_display_timezone' ? 'translated' : $key;
            }

            public function locale(): string
            {
                return 'en';
            }
        };

        self::assertSame('translated', $invalidTenantDisplayTimezoneException->userMessage($translator));
    }

    #[Test]
    public function it_maps_to_unprocessable_entity(): void
    {
        $invalidTenantDisplayTimezoneException = new InvalidTenantDisplayTimezoneException('x');

        self::assertSame(HttpStatus::UNPROCESSABLE_ENTITY, $invalidTenantDisplayTimezoneException->statusCode());
    }
}
