<?php

declare(strict_types=1);

use App\Application\Pagination\Pagination;

it('uses default values', function (): void {
    $pagination = new Pagination;

    expect($pagination->page)->toBe(1)
        ->and($pagination->perPage)->toBe(Pagination::DEFAULT_PER_PAGE);
});

it('accepts custom page and perPage', function (): void {
    $pagination = new Pagination(3, 25);

    expect($pagination->page)->toBe(3)
        ->and($pagination->perPage)->toBe(25);
});

it('clamps page to minimum of 1', function (): void {
    $pagination = new Pagination(0);

    expect($pagination->page)->toBe(1);

    $pagination = new Pagination(-5);

    expect($pagination->page)->toBe(1);
});

it('clamps perPage to minimum of 1', function (): void {
    $pagination = new Pagination(1, 0);

    expect($pagination->perPage)->toBe(1);

    $pagination = new Pagination(1, -10);

    expect($pagination->perPage)->toBe(1);
});

it('clamps perPage to maximum', function (): void {
    $pagination = new Pagination(1, 200);

    expect($pagination->perPage)->toBe(Pagination::MAX_PER_PAGE);
});

it('calculates offset correctly', function (): void {
    expect(new Pagination(1, 15)->offset())->toBe(0)
        ->and(new Pagination(2, 15)->offset())->toBe(15)
        ->and(new Pagination(3, 10)->offset())->toBe(20)
        ->and(new Pagination(1, 100)->offset())->toBe(0);
});
