<?php

declare(strict_types=1);

namespace App\Presentation\Http\Serialization;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * Serializes instants for JSON and HTML datetime attributes as RFC 3339 in UTC.
 */
final class InstantJson
{
    public static function toRfc3339Utc(DateTimeInterface $value): string
    {
        $immutable = DateTimeImmutable::createFromInterface($value);

        return $immutable->setTimezone(new DateTimeZone('UTC'))
            ->format(DateTimeInterface::RFC3339_EXTENDED);
    }
}
