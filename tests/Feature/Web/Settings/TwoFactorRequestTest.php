<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Auth\VerifyTwoFactorChallengeRequest;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use App\Presentation\Http\Request\Web\Settings\UpdateOwnTwoFactorRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

it('resolves tenant settings tab for password rotation and two factor', function (): void {
    $showTenantSettingsRequest = ShowTenantSettingsRequest::create('/settings', 'GET', [
        'tab' => ShowTenantSettingsRequest::PASSWORD_ROTATION_TAB,
    ]);
    $showTenantSettingsRequest->setContainer(app());
    $showTenantSettingsRequest->validateResolved();

    $twoFactorRequest = ShowTenantSettingsRequest::create('/settings', 'GET', [
        'tab' => ShowTenantSettingsRequest::TWO_FACTOR_TAB,
    ]);
    $twoFactorRequest->setContainer(app());
    $twoFactorRequest->validateResolved();

    $defaultRequest = ShowTenantSettingsRequest::create('/settings', 'GET');
    $defaultRequest->setContainer(app());
    $defaultRequest->validateResolved();

    expect($showTenantSettingsRequest->activeTab())->toBe(ShowTenantSettingsRequest::PASSWORD_ROTATION_TAB)
        ->and($twoFactorRequest->activeTab())->toBe(ShowTenantSettingsRequest::TWO_FACTOR_TAB)
        ->and($defaultRequest->activeTab())->toBe('general');
});

it('falls back to general tab when tab query is invalid string', function (): void {
    $showTenantSettingsRequest = ShowTenantSettingsRequest::create('/settings', 'GET', [
        'tab' => 'invalid-tab',
    ]);

    expect($showTenantSettingsRequest->activeTab())->toBe('general');
});

it('validates update own two factor request actions', function (): void {
    $updateOwnTwoFactorRequest = UpdateOwnTwoFactorRequest::create('/profile/two-factor', 'PUT', [
        'action' => 'totp-confirm',
        'totp_code' => '123456',
    ]);
    $updateOwnTwoFactorRequest->setContainer(app());
    $updateOwnTwoFactorRequest->validateResolved();

    expect($updateOwnTwoFactorRequest->authorize())->toBeTrue();
});

it('validates email save action with email two factor flag', function (): void {
    $updateOwnTwoFactorRequest = UpdateOwnTwoFactorRequest::create('/profile/two-factor', 'PUT', [
        'action' => 'email-save',
        'email_two_factor_enabled' => '1',
    ]);
    $updateOwnTwoFactorRequest->setContainer(app());
    $updateOwnTwoFactorRequest->validateResolved();

    expect($updateOwnTwoFactorRequest->authorize())->toBeTrue();
});

it('validates totp save action with totp two factor flag', function (): void {
    $updateOwnTwoFactorRequest = UpdateOwnTwoFactorRequest::create('/profile/two-factor', 'PUT', [
        'action' => 'totp-save',
        'totp_two_factor_enabled' => '1',
    ]);
    $updateOwnTwoFactorRequest->setContainer(app());
    $updateOwnTwoFactorRequest->validateResolved();

    expect($updateOwnTwoFactorRequest->authorize())->toBeTrue();
});

it('rejects invalid update own two factor action', function (): void {
    $updateOwnTwoFactorRequest = UpdateOwnTwoFactorRequest::create('/profile/two-factor', 'PUT', [
        'action' => 'unknown',
    ]);
    $updateOwnTwoFactorRequest->setContainer(app());
    $updateOwnTwoFactorRequest->setRedirector(app(Redirector::class));

    expect(fn () => $updateOwnTwoFactorRequest->validateResolved())->toThrow(ValidationException::class);
});

it('validates verify two factor challenge request', function (): void {
    $verifyTwoFactorChallengeRequest = VerifyTwoFactorChallengeRequest::create('/two-factor/verify', 'POST', [
        'method' => 'totp',
        'code' => '123456',
    ]);
    $verifyTwoFactorChallengeRequest->setContainer(app());
    $verifyTwoFactorChallengeRequest->validateResolved();

    expect($verifyTwoFactorChallengeRequest->authorize())->toBeTrue();
});

it('rejects invalid verify two factor challenge payload', function (): void {
    $verifyTwoFactorChallengeRequest = VerifyTwoFactorChallengeRequest::create('/two-factor/verify', 'POST', [
        'method' => 'sms',
        'code' => '12',
    ]);
    $verifyTwoFactorChallengeRequest->setContainer(app());
    $verifyTwoFactorChallengeRequest->setRedirector(app(Redirector::class));

    expect(fn () => $verifyTwoFactorChallengeRequest->validateResolved())->toThrow(ValidationException::class);
});
