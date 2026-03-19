<?php

declare(strict_types=1);

use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberHandler;
use App\Domain\Organization\Event\MemberRemoved;
use App\Domain\Organization\Exception\MemberNotFoundException;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeOrganizationMemberRepository;

it('removes a member and emits event', function (): void {
    $memberRepository = new FakeOrganizationMemberRepository([
        '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveMemberHandler($memberRepository, $eventCollector);

    $handler->handle(new RemoveMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($memberRepository->removed)->toHaveCount(1)
        ->and($memberRepository->removed[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(MemberRemoved::class);
});

it('throws when user is not a member', function (): void {
    $memberRepository = new FakeOrganizationMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveMemberHandler($memberRepository, $eventCollector);

    $handler->handle(new RemoveMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(MemberNotFoundException::class);
