<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;

it('includes rate limit headers on API response', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440910',
        'name' => 'API User',
        'email' => 'api-throttle@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/users/'.$user->id)
        ->assertHeader('X-RateLimit-Limit', '60');
});

it('returns 429 when API limit exceeded', function (): void {
    RateLimiter::for('api', static fn (Request $request): Limit => Limit::perMinute(3)->by($request->user()?->getAuthIdentifier() ?? $request->ip()));

    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440911',
        'name' => 'API User',
        'email' => 'api-throttle2@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    for ($i = 0; $i < 3; $i++) {
        $this->getJson('/api/v1/users/'.$user->id)->assertOk();
    }

    $this->getJson('/api/v1/users/'.$user->id)->assertStatus(429);
});
