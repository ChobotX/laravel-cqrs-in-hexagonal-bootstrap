<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\ManageUserPermissionsCommand;
use App\Domain\Authorization\Contract\Command\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Contract\Command\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Contract\Command\SetPermissionOverrideCommand;
use App\Domain\Authorization\Contract\Enum\UserPermissionAction;
use App\Domain\Authorization\Exception\InvalidUserPermissionActionPayloadException;
use App\Domain\Authorization\Handler\Command\ManageUserPermissionsHandler;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeTranslator;

it('dispatches AssignRoleToUserCommand for AssignRole action', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageUserPermissionsHandler($bus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::AssignRole,
        roleId: 'r-1',
    ));

    expect($bus->dispatched)->toHaveCount(1)
        ->and($bus->dispatched[0])->toBeInstanceOf(AssignRoleToUserCommand::class);
});

it('dispatches RevokeRoleFromUserCommand for RevokeRole action', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageUserPermissionsHandler($bus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::RevokeRole,
        roleId: 'r-1',
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(RevokeRoleFromUserCommand::class);
});

it('dispatches SetPermissionOverrideCommand for SetOverride action', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageUserPermissionsHandler($bus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::SetOverride,
        permission: 'users.list.read',
        overrideType: 'grant',
        overrideScope: 'all',
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(SetPermissionOverrideCommand::class);
});

it('dispatches RemovePermissionOverrideCommand for RemoveOverride action', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageUserPermissionsHandler($bus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::RemoveOverride,
        permission: 'users.list.read',
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(RemovePermissionOverrideCommand::class);
});

it('throws when AssignRole has missing roleId', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::AssignRole,
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('throws when RevokeRole has missing roleId', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::RevokeRole,
        roleId: '',
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('throws when SetOverride has missing permission', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::SetOverride,
        permission: null,
        overrideType: 'grant',
        overrideScope: 'all',
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('throws when RemoveOverride has missing permission', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::RemoveOverride,
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('throws when SetOverride has missing overrideType', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::SetOverride,
        permission: 'users.list.read',
        overrideType: null,
        overrideScope: 'all',
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('throws when SetOverride has missing overrideScope', function (): void {
    $handler = new ManageUserPermissionsHandler(new FakeCommandBus);

    $handler->handle(new ManageUserPermissionsCommand(
        userId: 'u-1',
        action: UserPermissionAction::SetOverride,
        permission: 'users.list.read',
        overrideType: 'grant',
        overrideScope: null,
    ));
})->throws(InvalidUserPermissionActionPayloadException::class);

it('exposes a user-friendly message and unprocessable status', function (): void {
    $exception = new InvalidUserPermissionActionPayloadException('assign_role', 'roleId');

    expect($exception->userMessage(new FakeTranslator))
        ->toContain('messages.exceptions.invalid_user_permission_action_payload')
        ->and($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY);
});
