<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\SyncUserRolesCommand;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\User\Contract\Command\UpdateUserCommand;
use App\Domain\User\Contract\Command\UpdateUserWithAvatarAndRelationsCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\UpdateUserWithAvatarAndRelationsHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeQueryBus;

function updateUserFixture(?string $avatarFileId = null): User
{
    return new User(
        id: new UserId('11111111-1111-1111-1111-111111111111'),
        name: new UserName('Old Name'),
        email: new Email('old@example.com'),
        isActivated: true,
        avatarFileId: $avatarFileId !== null ? new FileId($avatarFileId) : null,
    );
}

function updateUserAvatarFixture(): FileUpload
{
    return new FileUpload(
        originalName: new FileName('avatar.jpg'),
        mimeType: new MimeType('image/jpeg'),
        sizeInBytes: 1024,
        file: new SplFileInfo(__FILE__),
    );
}

it('uses existing email when command email is null', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture('22222222-2222-2222-2222-222222222222')]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New Name',
        email: null,
        actorId: 'actor-1',
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateUserCommand);
    expect($update->email)->toBe('old@example.com')
        ->and($update->avatarFileId)->toBe('22222222-2222-2222-2222-222222222222');
});

it('uses provided email when command email is set', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: 'new@example.com',
        actorId: 'actor-1',
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateUserCommand);
    expect($update->email)->toBe('new@example.com');
});

it('clears avatarFileId when removeAvatar is true', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture('22222222-2222-2222-2222-222222222222')]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
        removeAvatar: true,
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateUserCommand);
    expect($update->avatarFileId)->toBeNull();
});

it('stores avatar and uses generated id when upload present', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
        avatarUpload: updateUserAvatarFixture(),
    ));

    $store = $bus->dispatched[0];
    assert($store instanceof StoreAvatarCommand);
    $update = $bus->dispatched[1];
    assert($update instanceof UpdateUserCommand);
    expect($update->avatarFileId)->toBe($store->id);
});

it('preserves existing avatar when no upload and no remove', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture('22222222-2222-2222-2222-222222222222')]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateUserCommand);
    expect($update->avatarFileId)->toBe('22222222-2222-2222-2222-222222222222');
});

it('dispatches SyncUserRolesCommand when roleIds not null', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
        roleIds: ['r-1'],
    ));

    $sync = $bus->dispatched[1];
    assert($sync instanceof SyncUserRolesCommand);
    expect($sync->submittedRoleIds)->toBe(['r-1']);
});

it('dispatches SyncUserTeamsCommand when teamIds not null', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
        teamIds: [],
    ));

    $sync = $bus->dispatched[1];
    assert($sync instanceof SyncUserTeamsCommand);
    expect($sync->submittedTeamIds)->toBe([]);
});

it('dispatches SyncEntityLabelsCommand when labelIds not null', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
        labelIds: ['l-1', 'l-2'],
    ));

    $sync = $bus->dispatched[1];
    assert($sync instanceof SyncEntityLabelsCommand);
    expect($sync->entityType)->toBe('users')
        ->and($sync->submittedLabelIds)->toBe(['l-1', 'l-2']);
});

it('skips relation syncs when all relation lists are null', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetUserByIdQuery::class => updateUserFixture()]);
    $handler = new UpdateUserWithAvatarAndRelationsHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateUserWithAvatarAndRelationsCommand(
        id: '11111111-1111-1111-1111-111111111111',
        name: 'New',
        email: null,
        actorId: 'actor-1',
    ));

    expect($bus->dispatched)->toHaveCount(1);
    expect($bus->dispatched[0])->toBeInstanceOf(UpdateUserCommand::class);
});
