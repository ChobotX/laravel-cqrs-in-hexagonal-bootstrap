<?php

declare(strict_types=1);

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\ValueObject\TwoFactorUiStatus;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Auth\IssueTwoFactorEmailCodeController;
use App\Presentation\Http\Controller\Web\Auth\ShowTwoFactorChallengeController;
use App\Presentation\Http\Controller\Web\Auth\VerifyTwoFactorChallengeController;
use App\Presentation\Http\Request\Web\Auth\VerifyTwoFactorChallengeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('covers show, issue and verify two-factor challenge controllers', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440889',
        'name' => 'Two Factor Auth User',
        'email' => 'two-factor-auth@example.com',
        'password' => Hash::make('password'),
    ]);
    Auth::login($user);

    $mock = Mockery::mock(QueryBus::class);
    $mock->shouldReceive('dispatch')->once()->andReturn(new TwoFactorUiStatus(true, false, true, true, true, false));
    $showController = new ShowTwoFactorChallengeController($mock);
    $showView = $showController();

    $commandBus = Mockery::mock(CommandBus::class);
    $commandBus->shouldReceive('dispatch')->once();
    $issueController = new IssueTwoFactorEmailCodeController($commandBus);
    $redirectResponse = $issueController();

    $commandBusVerify = Mockery::mock(CommandBus::class);
    $commandBusVerify->shouldReceive('dispatch')->once();
    $verifyController = new VerifyTwoFactorChallengeController($commandBusVerify);
    $verifyTwoFactorChallengeRequest = VerifyTwoFactorChallengeRequest::create('/two-factor/verify', 'POST', ['method' => 'totp', 'code' => '123456']);
    $verifyTwoFactorChallengeRequest->setContainer(app());

    $verifyResponse = $verifyController($verifyTwoFactorChallengeRequest);

    expect($showView->getName())->toBe('auth.two-factor-challenge')
        ->and($redirectResponse->getTargetUrl())->toContain('/two-factor')
        ->and($verifyResponse->getTargetUrl())->toContain('/users');
});
