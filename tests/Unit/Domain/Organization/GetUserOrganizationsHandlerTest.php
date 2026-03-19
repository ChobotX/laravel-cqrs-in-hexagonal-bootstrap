<?php

declare(strict_types=1);

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsHandler;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery;
use Tests\Helper\FakeOrganizationMemberRepository;
use Tests\Helper\FakeOrganizationRepository;

it('returns organizations user is a member of', function (): void {
    $org = new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Acme Corp'),
        new OrganizationSlug('acme-corp'),
        'Org 1',
    );

    $memberRepository = new FakeOrganizationMemberRepository([
        '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
    ]);

    $orgRepository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => $org]);

    $handler = new GetUserOrganizationsHandler($memberRepository, $orgRepository);

    $result = $handler->handle(new GetUserOrganizationsQuery('00000000-0000-0000-0000-000000000010'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Acme Corp');
});

it('returns empty when user has no memberships', function (): void {
    $memberRepository = new FakeOrganizationMemberRepository;
    $orgRepository = new FakeOrganizationRepository;

    $handler = new GetUserOrganizationsHandler($memberRepository, $orgRepository);

    $result = $handler->handle(new GetUserOrganizationsQuery('00000000-0000-0000-0000-000000000010'));

    expect($result)->toBeEmpty();
});
