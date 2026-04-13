<?php

declare(strict_types=1);

use App\Domain\Notification\Service\DefaultRecipientResolver;
use Tests\Helper\FakeTeamMemberRepository;

it('delegates resolveTeamMembers to team member repository', function (): void {
    $repo = new FakeTeamMemberRepository([
        'u1' => ['team-a'],
        'u2' => ['team-a'],
    ]);
    $resolver = new DefaultRecipientResolver($repo);

    expect($resolver->resolveTeamMembers('team-a'))->toBe(['u1', 'u2']);
});

it('delegates resolveTeamWithSubteamMembers to team member repository', function (): void {
    $repo = new FakeTeamMemberRepository;
    $resolver = new DefaultRecipientResolver($repo);

    expect($resolver->resolveTeamWithSubteamMembers('any'))->toBe([]);
});
