<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\RevokeRecordShareCommand;
use App\Domain\Authorization\Contract\Command\ShareRecordCommand;
use App\Domain\File\Contract\Command\DeleteFileCommand;
use App\Domain\File\Contract\Command\StoreFileCommand;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Label\Contract\Command\AssignLabelCommand;
use App\Domain\Label\Contract\Command\RemoveLabelCommand;
use App\Domain\Registry\Contract\Command\DeprecateDefinitionVersionCommand;

it('ShareRecordCommand exposes audit entity', function (): void {
    $command = new ShareRecordCommand('user-1', 'role', 'role-1', 'read', 'admin-1');

    expect($command->auditEntityType())->toBe('role')
        ->and($command->auditEntityId())->toBe('role-1');
});

it('DeleteFileCommand exposes audit entity', function (): void {
    $command = new DeleteFileCommand('file-1');

    expect($command->auditEntityType())->toBe('file')
        ->and($command->auditEntityId())->toBe('file-1');
});

it('StoreFileCommand exposes audit entity', function (): void {
    $upload = new FileUpload(new FileName('test.png'), new MimeType('image/png'), 1024, new SplFileInfo('/tmp/test.png'));
    $command = new StoreFileCommand('file-1', 'avatars', 'user-1', $upload);

    expect($command->auditEntityType())->toBe('file')
        ->and($command->auditEntityId())->toBe('file-1');
});

it('AssignLabelCommand exposes audit entity', function (): void {
    $command = new AssignLabelCommand('label-1', 'entity-1', 'users');

    expect($command->auditEntityType())->toBe('label')
        ->and($command->auditEntityId())->toBe('label-1');
});

it('RemoveLabelCommand exposes audit entity', function (): void {
    $command = new RemoveLabelCommand('label-1', 'entity-1');

    expect($command->auditEntityType())->toBe('label')
        ->and($command->auditEntityId())->toBe('label-1');
});

it('RevokeRecordShareCommand exposes audit entity', function (): void {
    $command = new RevokeRecordShareCommand('user-1', 'role', 'role-1');

    expect($command->auditEntityType())->toBe('role')
        ->and($command->auditEntityId())->toBe('role-1');
});

it('DeprecateDefinitionVersionCommand exposes audit entity', function (): void {
    $command = new DeprecateDefinitionVersionCommand('version-1');

    expect($command->auditEntityType())->toBe('definition')
        ->and($command->auditEntityId())->toBe('version-1');
});
