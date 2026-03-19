<?php

declare(strict_types=1);

use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\AddMember\AddMemberHandler;
use App\Domain\Organization\Event\MemberAdded;
use App\Domain\Organization\Exception\MemberAlreadyExistsException;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeOrganizationMemberRepository;
use Tests\Helper\FakeOrganizationRepository;
use Tests\Helper\FakeQueryBus;

function addMemberOrg(): Organization
{
    return new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Test Org'),
        new OrganizationSlug('test-org'),
        'Test',
    );
}

it('adds a member and emits event', function (): void {
    $user = new User(
        new UserId('00000000-0000-0000-0000-000000000010'),
        'John Doe',
        new Email('john@test.com'),
    );

    $orgRepository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => addMemberOrg()]);
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => $user]);
    $memberRepository = new FakeOrganizationMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AddMemberHandler($memberRepository, $orgRepository, $queryBus, $eventCollector);

    $handler->handle(new AddMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($memberRepository->added)->toHaveCount(1)
        ->and($memberRepository->added[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010')
        ->and($memberRepository->added[0]['organizationId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(MemberAdded::class);
});

it('throws when user already a member', function (): void {
    $user = new User(
        new UserId('00000000-0000-0000-0000-000000000010'),
        'John Doe',
        new Email('john@test.com'),
    );

    $orgRepository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => addMemberOrg()]);
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => $user]);
    $memberRepository = new FakeOrganizationMemberRepository([
        '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new AddMemberHandler($memberRepository, $orgRepository, $queryBus, $eventCollector);

    $handler->handle(new AddMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(MemberAlreadyExistsException::class);

it('throws when user does not exist', function (): void {
    $orgRepository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => addMemberOrg()]);
    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => new UserNotFoundException('00000000-0000-0000-0000-000000000010'),
    ]);
    $memberRepository = new FakeOrganizationMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AddMemberHandler($memberRepository, $orgRepository, $queryBus, $eventCollector);

    $handler->handle(new AddMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(UserNotFoundException::class);

it('throws when organization does not exist', function (): void {
    $orgRepository = new FakeOrganizationRepository;
    $queryBus = new FakeQueryBus;
    $memberRepository = new FakeOrganizationMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AddMemberHandler($memberRepository, $orgRepository, $queryBus, $eventCollector);

    $handler->handle(new AddMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(OrganizationNotFoundException::class);
