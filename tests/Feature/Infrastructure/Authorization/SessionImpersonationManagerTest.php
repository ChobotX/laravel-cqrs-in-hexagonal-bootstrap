<?php

declare(strict_types=1);

use App\Infrastructure\Authorization\SessionImpersonationManager;
use App\Infrastructure\Eloquent\Authorization\ImpersonationSessionModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

function impersonationManager(?string $token = null, ?string $guardId = null): SessionImpersonationManager
{
    $request = Request::create('/test');

    if ($token !== null) {
        $request->headers->set('X-Impersonate-Token', $token);
    }

    $mock = Mockery::mock(Guard::class);
    $mock->allows('id')->andReturn($guardId);

    return new SessionImpersonationManager($request, $mock);
}

it('starts an impersonation session', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440400', 'name' => 'Imp1', 'email' => 'imp1@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440401', 'name' => 'Imp2', 'email' => 'imp2@t.com']);
    $sessionImpersonationManager = impersonationManager();
    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440400', '550e8400-e29b-41d4-a716-446655440401');

    $session = ImpersonationSessionModel::first();

    expect($session)->not->toBeNull();
    expect($session->impersonator_user_id)->toBe('550e8400-e29b-41d4-a716-446655440400');
    expect($session->impersonated_user_id)->toBe('550e8400-e29b-41d4-a716-446655440401');
    expect($session->ended_at)->toBeNull();
});

it('reports inactive when no token and no guard', function (): void {
    $sessionImpersonationManager = impersonationManager();

    expect($sessionImpersonationManager->isActive())->toBeFalse();
    expect($sessionImpersonationManager->realUserId())->toBeNull();
    expect($sessionImpersonationManager->impersonatedUserId())->toBeNull();
});

it('reports active with valid token', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440402', 'name' => 'Imp3', 'email' => 'imp3@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440403', 'name' => 'Imp4', 'email' => 'imp4@t.com']);
    $sessionImpersonationManager = impersonationManager();
    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440402', '550e8400-e29b-41d4-a716-446655440403');

    $session = ImpersonationSessionModel::first();
    $withTokenManager = impersonationManager($session->token);

    expect($withTokenManager->isActive())->toBeTrue();
    expect($withTokenManager->realUserId())->toBe('550e8400-e29b-41d4-a716-446655440402');
    expect($withTokenManager->impersonatedUserId())->toBe('550e8400-e29b-41d4-a716-446655440403');
});

it('stops impersonation session', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440404', 'name' => 'Imp5', 'email' => 'imp5@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440405', 'name' => 'Imp6', 'email' => 'imp6@t.com']);
    $sessionImpersonationManager = impersonationManager();
    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440404', '550e8400-e29b-41d4-a716-446655440405');

    $session = ImpersonationSessionModel::first();
    $withTokenManager = impersonationManager($session->token);
    $withTokenManager->stop();

    $session->refresh();
    expect($session->ended_at)->not->toBeNull();
    expect($withTokenManager->isActive())->toBeFalse();
});

it('finds active session via guard id', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440406', 'name' => 'Imp7', 'email' => 'imp7@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440407', 'name' => 'Imp8', 'email' => 'imp8@t.com']);
    $sessionImpersonationManager = impersonationManager();
    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440406', '550e8400-e29b-41d4-a716-446655440407');

    $guardManager = impersonationManager(null, '550e8400-e29b-41d4-a716-446655440406');

    expect($guardManager->isActive())->toBeTrue();
    expect($guardManager->realUserId())->toBe('550e8400-e29b-41d4-a716-446655440406');
    expect($guardManager->impersonatedUserId())->toBe('550e8400-e29b-41d4-a716-446655440407');
});

it('header takes priority over guard lookup', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440408', 'name' => 'Imp9', 'email' => 'imp9@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440409', 'name' => 'Imp10', 'email' => 'imp10@t.com']);
    $sessionImpersonationManager = impersonationManager();
    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440408', '550e8400-e29b-41d4-a716-446655440409');

    $session = ImpersonationSessionModel::first();
    $tokenManager = impersonationManager($session->token, '550e8400-e29b-41d4-a716-446655440408');

    expect($tokenManager->isActive())->toBeTrue();
    expect($tokenManager->impersonatedUserId())->toBe('550e8400-e29b-41d4-a716-446655440409');
});

it('starting new session ends previous active session for same impersonator', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440411', 'name' => 'Imp11', 'email' => 'imp11@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440412', 'name' => 'Imp12', 'email' => 'imp12@t.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440413', 'name' => 'Imp13', 'email' => 'imp13@t.com']);
    $sessionImpersonationManager = impersonationManager();

    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440411', '550e8400-e29b-41d4-a716-446655440412');

    $firstSession = ImpersonationSessionModel::whereNull('ended_at')->first();
    expect($firstSession->impersonated_user_id)->toBe('550e8400-e29b-41d4-a716-446655440412');

    $sessionImpersonationManager->start('550e8400-e29b-41d4-a716-446655440411', '550e8400-e29b-41d4-a716-446655440413');

    $firstSession->refresh();
    expect($firstSession->ended_at)->not->toBeNull();

    $activeSessions = ImpersonationSessionModel::where('impersonator_user_id', '550e8400-e29b-41d4-a716-446655440411')
        ->whereNull('ended_at')
        ->get();
    expect($activeSessions)->toHaveCount(1);
    expect($activeSessions->first()->impersonated_user_id)->toBe('550e8400-e29b-41d4-a716-446655440413');
});

it('guard with no active session returns inactive', function (): void {
    $sessionImpersonationManager = impersonationManager(null, '550e8400-e29b-41d4-a716-446655440410');

    expect($sessionImpersonationManager->isActive())->toBeFalse();
});
