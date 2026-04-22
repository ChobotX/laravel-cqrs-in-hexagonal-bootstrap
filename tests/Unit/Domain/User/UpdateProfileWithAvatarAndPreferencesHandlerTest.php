<?php

declare(strict_types=1);

use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Command\UpdateProfileWithAvatarAndPreferencesCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\UpdateProfileWithAvatarAndPreferencesHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeQueryBus;

function profileFixture(?string $avatarFileId = null): User
{
    return new User(
        id: new UserId('11111111-1111-1111-1111-111111111111'),
        name: new UserName('Me'),
        email: new Email('me@example.com'),
        isActivated: true,
        avatarFileId: $avatarFileId !== null ? new FileId($avatarFileId) : null,
    );
}

function profileAvatarFixture(): FileUpload
{
    return new FileUpload(
        originalName: new FileName('avatar.jpg'),
        mimeType: new MimeType('image/jpeg'),
        sizeInBytes: 1024,
        file: new SplFileInfo(__FILE__),
    );
}

it('dispatches only UpdateProfileCommand when no avatar and no preferences', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture()]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me Updated',
    ));

    expect($bus->dispatched)->toHaveCount(1);
    $update = $bus->dispatched[0];
    assert($update instanceof UpdateProfileCommand);
    expect($update->avatarFileId)->toBeNull();
});

it('preserves existing avatar when no upload and no remove', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture('22222222-2222-2222-2222-222222222222')]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me',
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateProfileCommand);
    expect($update->avatarFileId)->toBe('22222222-2222-2222-2222-222222222222');
});

it('clears avatar when removeAvatar is true', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture('22222222-2222-2222-2222-222222222222')]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me',
        removeAvatar: true,
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateProfileCommand);
    expect($update->avatarFileId)->toBeNull();
});

it('stores avatar and uses generated id when upload present', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture()]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me',
        avatarUpload: profileAvatarFixture(),
    ));

    $store = $bus->dispatched[0];
    assert($store instanceof StoreAvatarCommand);
    $update = $bus->dispatched[1];
    assert($update instanceof UpdateProfileCommand);
    expect($update->avatarFileId)->toBe($store->id);
});

it('dispatches UpdateNotificationPreferencesCommand when preferences provided', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture()]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me',
        notificationPreferences: ['info' => ['email']],
    ));

    $pref = $bus->dispatched[1];
    assert($pref instanceof UpdateNotificationPreferencesCommand);
    expect($pref->preferences)->toBe(['info' => ['email']]);
});

it('forwards password and email when provided', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetOwnProfileQuery::class => profileFixture()]);
    $handler = new UpdateProfileWithAvatarAndPreferencesHandler($bus, $queryBus, new FakeIdGenerator);

    $handler->handle(new UpdateProfileWithAvatarAndPreferencesCommand(
        userId: '11111111-1111-1111-1111-111111111111',
        name: 'Me',
        email: 'new@example.com',
        rawPassword: 'secret-pass',
    ));

    $update = $bus->dispatched[0];
    assert($update instanceof UpdateProfileCommand);
    expect($update->email)->toBe('new@example.com')
        ->and($update->rawPassword)->toBe('secret-pass');
});
