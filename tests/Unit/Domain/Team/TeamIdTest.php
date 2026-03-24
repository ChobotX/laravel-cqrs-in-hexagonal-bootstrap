<?php

declare(strict_types=1);

use App\Domain\Team\Exception\InvalidTeamIdException;
use App\Domain\Team\TeamId;

it('creates a valid team id', function (): void {
    $id = new TeamId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on invalid uuid', function (): void {
    new TeamId('not-a-uuid');
})->throws(InvalidTeamIdException::class);

it('throws on empty string', function (): void {
    new TeamId('');
})->throws(InvalidTeamIdException::class);

it('compares equality', function (): void {
    $a = new TeamId('550e8400-e29b-41d4-a716-446655440000');
    $b = new TeamId('550e8400-e29b-41d4-a716-446655440000');
    $c = new TeamId('660e8400-e29b-41d4-a716-446655440000');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
