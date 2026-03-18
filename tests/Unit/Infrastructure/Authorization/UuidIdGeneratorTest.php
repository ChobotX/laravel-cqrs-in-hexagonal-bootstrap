<?php

declare(strict_types=1);

use App\Infrastructure\UuidIdGenerator;

it('generates a valid uuid', function (): void {
    $generator = new UuidIdGenerator;
    $id = $generator->generate();

    expect($id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

it('generates unique ids', function (): void {
    $generator = new UuidIdGenerator;

    expect($generator->generate())->not->toBe($generator->generate());
});
