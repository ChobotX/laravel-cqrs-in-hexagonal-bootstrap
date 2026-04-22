<?php

declare(strict_types=1);

use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\AvatarNamespace;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Command\CreateUserWithAvatarCommand;
use App\Domain\User\Handler\Command\CreateUserWithAvatarHandler;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeIdGenerator;

function createUserAvatarFixture(): FileUpload
{
    return new FileUpload(
        originalName: new FileName('avatar.jpg'),
        mimeType: new MimeType('image/jpeg'),
        sizeInBytes: 1024,
        file: new SplFileInfo(__FILE__),
    );
}

it('dispatches only CreateUserCommand when no avatar', function (): void {
    $bus = new FakeCommandBus;
    $handler = new CreateUserWithAvatarHandler($bus, new FakeIdGenerator);

    $handler->handle(new CreateUserWithAvatarCommand(
        id: 'user-1',
        name: 'Jane',
        email: 'jane@example.com',
        uploadedBy: 'actor-1',
    ));

    expect($bus->dispatched)->toHaveCount(1);
    $createUser = $bus->dispatched[0];
    assert($createUser instanceof CreateUserCommand);
    expect($createUser->avatarFileId)->toBeNull();
});

it('dispatches StoreAvatarCommand then CreateUserCommand with generated avatar id', function (): void {
    $bus = new FakeCommandBus;
    $handler = new CreateUserWithAvatarHandler($bus, new FakeIdGenerator);

    $handler->handle(new CreateUserWithAvatarCommand(
        id: 'user-1',
        name: 'Jane',
        email: 'jane@example.com',
        uploadedBy: 'actor-1',
        avatarUpload: createUserAvatarFixture(),
    ));

    expect($bus->dispatched)->toHaveCount(2);

    $storeAvatar = $bus->dispatched[0];
    assert($storeAvatar instanceof StoreAvatarCommand);
    expect($storeAvatar->namespace)->toBe(AvatarNamespace::VALUE)
        ->and($storeAvatar->uploadedBy)->toBe('actor-1');

    $createUser = $bus->dispatched[1];
    assert($createUser instanceof CreateUserCommand);
    expect($createUser->avatarFileId)->toBe($storeAvatar->id);
});
