<?php

declare(strict_types=1);

use App\Domain\Notification\Service\DefaultRecipientResolver;
use App\Domain\Team\Contract\Query\ListTeamMemberUserIdsQuery;
use App\Domain\Team\Contract\Query\ListTeamSubtreeMemberUserIdsQuery;
use Tests\Helper\FakeQueryBus;

it('delegates resolveTeamMembers to the query bus', function (): void {
    $queryBus = new FakeQueryBus([
        ListTeamMemberUserIdsQuery::class => fn (ListTeamMemberUserIdsQuery $query): array => $query->teamId === 'team-a'
            ? ['u1', 'u2']
            : [],
    ]);
    $resolver = new DefaultRecipientResolver($queryBus);

    expect($resolver->resolveTeamMembers('team-a'))->toBe(['u1', 'u2']);
});

it('delegates resolveTeamWithSubteamMembers to the query bus', function (): void {
    $queryBus = new FakeQueryBus([
        ListTeamSubtreeMemberUserIdsQuery::class => [],
    ]);
    $resolver = new DefaultRecipientResolver($queryBus);

    expect($resolver->resolveTeamWithSubteamMembers('any'))->toBe([]);
});
