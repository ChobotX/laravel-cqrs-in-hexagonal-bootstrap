<?php

declare(strict_types=1);

use App\Infrastructure\UuidIdGenerator;

it('generates a valid uuid string', function (): void {
    $generator = new UuidIdGenerator;
    $uuid = $generator->generate();

    expect($uuid)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('generates unique ids on consecutive calls', function (): void {
    $generator = new UuidIdGenerator;

    $first = $generator->generate();
    $second = $generator->generate();

    expect($first)->not->toBe($second);
});
