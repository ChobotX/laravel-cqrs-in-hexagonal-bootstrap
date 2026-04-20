<?php

declare(strict_types=1);

use App\Infrastructure\Sso\LaravelSsoLoginSession;
use Illuminate\Contracts\Session\Session;

it('writes and pulls a user id through the session', function (): void {
    $session = Mockery::mock(Session::class);
    $session->shouldReceive('put')->once()->with('sso.last_resolved_user_id', 'user-1');
    $session->shouldReceive('pull')->once()->with('sso.last_resolved_user_id')->andReturn('user-1');

    $store = new LaravelSsoLoginSession($session);
    $store->setLastResolvedUserId('user-1');

    expect($store->pullLastResolvedUserId())->toBe('user-1');
});

it('returns null when the session value is missing or non-string', function (): void {
    $session = Mockery::mock(Session::class);
    $session->shouldReceive('pull')->andReturn(null);

    expect((new LaravelSsoLoginSession($session))->pullLastResolvedUserId())->toBeNull();
});
