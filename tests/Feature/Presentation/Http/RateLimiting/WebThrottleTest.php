<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

it('includes rate limit headers on web response', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440920',
        'name' => 'Web User',
        'email' => 'web-throttle@example.com',
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertHeader('X-RateLimit-Limit', '120');
});

it('returns 429 when web limit exceeded', function (): void {
    RateLimiter::for('web', static fn (Request $request): Limit => Limit::perMinute(3)->by($request->user()?->getAuthIdentifier() ?? $request->ip()));

    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440921',
        'name' => 'Web User',
        'email' => 'web-throttle2@example.com',
    ]);
    $this->assignSuperAdmin($user->id);

    for ($i = 0; $i < 3; $i++) {
        $this->actingAs($user)->get('/users')->assertOk();
    }

    $this->actingAs($user)->get('/users')->assertStatus(429);
});
