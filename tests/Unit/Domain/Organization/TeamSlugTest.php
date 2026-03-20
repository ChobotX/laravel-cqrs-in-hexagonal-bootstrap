<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\InvalidTeamSlugException;
use App\Domain\Organization\TeamSlug;

it('creates a valid team slug', function (): void {
    $slug = new TeamSlug('engineering');

    expect($slug->value)->toBe('engineering')
        ->and((string) $slug)->toBe('engineering');
});

it('allows hyphens in middle', function (): void {
    $slug = new TeamSlug('back-end');

    expect($slug->value)->toBe('back-end');
});

it('throws on too short', function (): void {
    new TeamSlug('a');
})->throws(InvalidTeamSlugException::class);

it('throws on uppercase', function (): void {
    new TeamSlug('Engineering');
})->throws(InvalidTeamSlugException::class);

it('throws on spaces', function (): void {
    new TeamSlug('back end');
})->throws(InvalidTeamSlugException::class);

it('throws on leading hyphen', function (): void {
    new TeamSlug('-backend');
})->throws(InvalidTeamSlugException::class);

it('throws on trailing hyphen', function (): void {
    new TeamSlug('backend-');
})->throws(InvalidTeamSlugException::class);

it('throws on too long', function (): void {
    new TeamSlug(str_repeat('a', 64));
})->throws(InvalidTeamSlugException::class);

it('accepts maximum length', function (): void {
    $slug = new TeamSlug(str_repeat('a', 63));

    expect(strlen($slug->value))->toBe(63);
});

it('compares equality', function (): void {
    $a = new TeamSlug('engineering');
    $b = new TeamSlug('engineering');
    $c = new TeamSlug('design');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
