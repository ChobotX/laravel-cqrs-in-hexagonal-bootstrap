<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Http\Serialization;

use App\Presentation\Http\Serialization\InstantJson;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function str_contains;
use function str_ends_with;

final class InstantJsonTest extends TestCase
{
    #[Test]
    public function it_serializes_utc_offset_in_rfc3339_extended(): void
    {
        $local = new DateTimeImmutable('2026-04-12 15:30:00', new DateTimeZone('Europe/Prague'));

        $out = InstantJson::toRfc3339Utc($local);

        self::assertStringStartsWith('2026-04-12T13:30:00', $out);
        self::assertTrue(
            str_ends_with($out, 'Z') || str_contains($out, '+00:00'),
            $out,
        );
    }
}
