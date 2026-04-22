<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\ValueObject\TotpSetup;
use App\Domain\User\Exception\InvalidTwoFactorSettingsActionPayloadException;
use App\Domain\User\Handler\Command\ManageOwnTwoFactorSettingsHandler;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeTranslator;

function totpSetup(?string $secret): TotpSetup
{
    return new TotpSetup($secret, null, false, null, false);
}

it('dispatches EnableEmailTwoFactorCommand for EmailSave with emailEnabled=true', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::EmailSave,
        emailEnabled: true,
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(EnableEmailTwoFactorCommand::class);
});

it('dispatches DisableEmailTwoFactorCommand for EmailSave with emailEnabled=false', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::EmailSave,
        emailEnabled: false,
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(DisableEmailTwoFactorCommand::class);
});

it('throws when EmailSave missing emailEnabled', function (): void {
    $handler = new ManageOwnTwoFactorSettingsHandler(new FakeCommandBus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::EmailSave,
    ));
})->throws(InvalidTwoFactorSettingsActionPayloadException::class);

it('dispatches StartTotpSetupCommand for TotpSave enable when no secret', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetTotpSetupQuery::class => totpSetup(null)]);
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, $queryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpSave,
        totpEnabled: true,
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(StartTotpSetupCommand::class);
});

it('does not dispatch when TotpSave enable with existing secret', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetTotpSetupQuery::class => totpSetup('SECRET')]);
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, $queryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpSave,
        totpEnabled: true,
    ));

    expect($bus->dispatched)->toBeEmpty();
});

it('dispatches DisableTotpTwoFactorCommand for TotpSave disable with secret present', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetTotpSetupQuery::class => totpSetup('SECRET')]);
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, $queryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpSave,
        totpEnabled: false,
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(DisableTotpTwoFactorCommand::class);
});

it('does not dispatch when TotpSave disable with no secret', function (): void {
    $bus = new FakeCommandBus;
    $queryBus = new FakeQueryBus([GetTotpSetupQuery::class => totpSetup(null)]);
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, $queryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpSave,
        totpEnabled: false,
    ));

    expect($bus->dispatched)->toBeEmpty();
});

it('throws when TotpSave missing totpEnabled', function (): void {
    $handler = new ManageOwnTwoFactorSettingsHandler(new FakeCommandBus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpSave,
    ));
})->throws(InvalidTwoFactorSettingsActionPayloadException::class);

it('dispatches ConfirmTotpSetupCommand with code', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpConfirm,
        totpCode: '123456',
    ));

    $confirm = $bus->dispatched[0];
    assert($confirm instanceof ConfirmTotpSetupCommand);
    expect($confirm->code)->toBe('123456');
});

it('throws when TotpConfirm missing code', function (): void {
    $handler = new ManageOwnTwoFactorSettingsHandler(new FakeCommandBus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpConfirm,
    ));
})->throws(InvalidTwoFactorSettingsActionPayloadException::class);

it('dispatches DisableTotpTwoFactorCommand for TotpDisable', function (): void {
    $bus = new FakeCommandBus;
    $handler = new ManageOwnTwoFactorSettingsHandler($bus, new FakeQueryBus);

    $handler->handle(new ManageOwnTwoFactorSettingsCommand(
        userId: 'u-1',
        action: TwoFactorSettingsAction::TotpDisable,
    ));

    expect($bus->dispatched[0])->toBeInstanceOf(DisableTotpTwoFactorCommand::class);
});

it('exception exposes user-friendly message and unprocessable status', function (): void {
    $exception = new InvalidTwoFactorSettingsActionPayloadException('email-save', 'emailEnabled');

    expect($exception->userMessage(new FakeTranslator))
        ->toContain('messages.exceptions.invalid_two_factor_settings_action_payload')
        ->and($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY);
});
