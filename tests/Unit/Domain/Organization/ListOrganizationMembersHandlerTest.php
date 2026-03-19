<?php

declare(strict_types=1);

use App\Domain\Organization\Query\ListOrganizationMembers\ListOrganizationMembersHandler;
use App\Domain\Organization\Query\ListOrganizationMembers\ListOrganizationMembersQuery;
use Tests\Helper\FakeOrganizationMemberRepository;

it('returns members for organization', function (): void {
    $memberRepository = new FakeOrganizationMemberRepository(
        [
            '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
            '00000000-0000-0000-0000-000000000020' => ['550e8400-e29b-41d4-a716-446655440000'],
        ],
        [
            '00000000-0000-0000-0000-000000000010' => ['name' => 'John', 'email' => 'john@test.com'],
            '00000000-0000-0000-0000-000000000020' => ['name' => 'Jane', 'email' => 'jane@test.com'],
        ],
    );

    $handler = new ListOrganizationMembersHandler($memberRepository);

    $result = $handler->handle(new ListOrganizationMembersQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toHaveCount(2)
        ->and($result[0]->userName)->toBe('John')
        ->and($result[1]->userName)->toBe('Jane');
});

it('returns empty when organization has no members', function (): void {
    $memberRepository = new FakeOrganizationMemberRepository;

    $handler = new ListOrganizationMembersHandler($memberRepository);

    $result = $handler->handle(new ListOrganizationMembersQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBeEmpty();
});
