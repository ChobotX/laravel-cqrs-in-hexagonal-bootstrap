<?php

declare(strict_types=1);

use App\Domain\Organization\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Domain\Organization\Command\RemoveTeamMember\RemoveTeamMemberHandler;
use App\Domain\Organization\Event\TeamMemberRemoved;
use App\Domain\Organization\Exception\TeamMemberNotFoundException;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamMemberRepository;

it('removes a team member and emits event', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveTeamMemberHandler($teamMemberRepo, $eventCollector);

    $handler->handle(new RemoveTeamMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($teamMemberRepo->removed)->toHaveCount(1)
        ->and($teamMemberRepo->removed[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamMemberRemoved::class);
});

it('throws when not a team member', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveTeamMemberHandler($teamMemberRepo, $eventCollector);

    $handler->handle(new RemoveTeamMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamMemberNotFoundException::class);
