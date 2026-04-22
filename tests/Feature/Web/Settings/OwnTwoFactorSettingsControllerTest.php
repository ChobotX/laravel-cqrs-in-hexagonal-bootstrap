<?php

declare(strict_types=1);

use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;
use App\Domain\User\Contract\ValueObject\TotpSetup;
use App\Domain\User\Contract\ValueObject\TwoFactorUiStatus;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Settings\ShowOwnTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Settings\ShowTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateOwnTwoFactorSettingsController;
use App\Presentation\Http\Request\Web\Settings\UpdateOwnTwoFactorRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('covers own two-factor settings controllers', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440890',
        'name' => 'Own Two Factor User',
        'email' => 'own-two-factor@example.com',
        'password' => Hash::make('password'),
    ]);
    Auth::login($user);

    $mock = Mockery::mock(QueryBus::class);
    $mock->shouldReceive('dispatch')->twice()->andReturn(
        new TwoFactorUiStatus(true, false, true, true, true, false),
        new TotpSetup('secret', 'otpauth://uri', false, ['AAAA-BBBB-CCCC-DDDD'], false),
    );

    $showController = new ShowOwnTwoFactorSettingsController($mock);
    $showView = $showController();

    $commandBus = Mockery::mock(CommandBus::class);
    $commandBus->shouldReceive('dispatch')
        ->times(5)
        ->with(Mockery::type(ManageOwnTwoFactorSettingsCommand::class));

    $updateController = new UpdateOwnTwoFactorSettingsController($commandBus);
    $actions = [
        ['action' => 'email-save', 'email_two_factor_enabled' => '1'],
        ['action' => 'email-save', 'email_two_factor_enabled' => '0'],
        ['action' => 'totp-save', 'totp_two_factor_enabled' => '1'],
        ['action' => 'totp-confirm', 'totp_code' => '123456'],
        ['action' => 'totp-disable'],
    ];
    $updateResponse = null;
    foreach ($actions as $action) {
        $request = UpdateOwnTwoFactorRequest::create('/profile/two-factor', 'PUT', $action);
        $request->setContainer(app());
        $updateResponse = $updateController($request);
    }

    $tabRedirect = (new ShowTwoFactorSettingsController)();

    expect($showView->getName())->toBe('settings.two-factor')
        ->and($updateResponse)->not->toBeNull()
        ->and($updateResponse->getTargetUrl())->toContain('/profile/two-factor')
        ->and($tabRedirect->getTargetUrl())->toContain('tab=two-factor');
});

it('exposes enum cases', function (): void {
    expect(TwoFactorSettingsAction::cases())->toHaveCount(4);
});
