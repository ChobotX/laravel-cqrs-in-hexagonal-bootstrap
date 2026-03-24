<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Team\Exception\InvalidTeamIdException;
use App\Domain\Team\Exception\InvalidTeamNameException;
use App\Domain\Team\Exception\InvalidTeamSlugException;
use App\Domain\Team\Exception\TeamCycleDetectedException;
use App\Domain\Team\Exception\TeamMemberAlreadyExistsException;
use App\Domain\Team\Exception\TeamMemberNotFoundException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;

function teamTestTranslator(): Translator
{
    return new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }
    };
}

it('InvalidTeamIdException has status 422 and translates', function (): void {
    $e = new InvalidTeamIdException('bad-id');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('bad-id')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.invalid_team_id:bad-id')
        ->and($e->invalidValue)->toBe('bad-id');
});

it('InvalidTeamNameException has status 422 and translates', function (): void {
    $e = new InvalidTeamNameException('');

    expect($e->statusCode())->toBe(422)
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.invalid_team_name:')
        ->and($e->invalidValue)->toBe('');
});

it('InvalidTeamSlugException has status 422 and translates', function (): void {
    $e = new InvalidTeamSlugException('BAD SLUG');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('BAD SLUG')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.invalid_team_slug:BAD SLUG')
        ->and($e->invalidValue)->toBe('BAD SLUG');
});

it('TeamNotFoundException has status 404 and translates', function (): void {
    $e = new TeamNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.team_not_found:550e8400-e29b-41d4-a716-446655440000')
        ->and($e->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('TeamSlugAlreadyExistsException has status 409 and translates', function (): void {
    $e = new TeamSlugAlreadyExistsException('backend');

    expect($e->statusCode())->toBe(409)
        ->and($e->getMessage())->toContain('backend')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.team_slug_already_exists:backend')
        ->and($e->slug)->toBe('backend');
});

it('TeamMemberAlreadyExistsException has status 409 and translates', function (): void {
    $e = new TeamMemberAlreadyExistsException('user-1', 'team-1');

    expect($e->statusCode())->toBe(409)
        ->and($e->getMessage())->toContain('user-1')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.team_member_already_exists:')
        ->and($e->userId)->toBe('user-1')
        ->and($e->teamId)->toBe('team-1');
});

it('TeamMemberNotFoundException has status 404 and translates', function (): void {
    $e = new TeamMemberNotFoundException('user-1', 'team-1');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('user-1')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.team_member_not_found:')
        ->and($e->userId)->toBe('user-1')
        ->and($e->teamId)->toBe('team-1');
});

it('TeamCycleDetectedException has status 422 and translates', function (): void {
    $e = new TeamCycleDetectedException('team-1', 'team-2');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('team-1')
        ->and($e->userMessage(teamTestTranslator()))->toBe('messages.exceptions.team_cycle_detected:')
        ->and($e->teamId)->toBe('team-1')
        ->and($e->parentTeamId)->toBe('team-2');
});
