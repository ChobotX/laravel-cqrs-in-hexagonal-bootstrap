<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Query\ListTeamMembersQuery;
use App\Domain\Team\Handler\Query\ListTeamMembersHandler;
use Tests\Helper\FakeTeamMemberRepository;

it('lists team members', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository(
        memberships: [
            'user-1' => ['team-1'],
            'user-2' => ['team-1'],
        ],
        userInfo: [
            'user-1' => ['name' => 'Alice', 'email' => 'alice@test.com'],
            'user-2' => ['name' => 'Bob', 'email' => 'bob@test.com'],
        ],
    );

    $handler = new ListTeamMembersHandler($teamMemberRepo);

    $result = $handler->handle(new ListTeamMembersQuery('team-1'));

    expect($result)->toHaveCount(2);
});

it('returns empty when no members', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository;
    $handler = new ListTeamMembersHandler($teamMemberRepo);

    $result = $handler->handle(new ListTeamMembersQuery('team-1'));

    expect($result)->toBe([]);
});
