<?php

declare(strict_types=1);

use App\Domain\Team\Exception\InvalidTeamNameException;
use App\Domain\Team\ValueObject\TeamName;

it('creates a valid team name', function (): void {
    $name = new TeamName('Engineering');

    expect($name->value)->toBe('Engineering')
        ->and((string) $name)->toBe('Engineering');
});

it('trims whitespace', function (): void {
    $name = new TeamName('  Backend  ');

    expect($name->value)->toBe('Backend');
});

it('throws on empty string', function (): void {
    new TeamName('');
})->throws(InvalidTeamNameException::class);

it('throws on whitespace-only string', function (): void {
    new TeamName('   ');
})->throws(InvalidTeamNameException::class);

it('compares equality', function (): void {
    $a = new TeamName('Engineering');
    $b = new TeamName('Engineering');
    $c = new TeamName('Design');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
