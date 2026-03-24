<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Hash;

it('sets user_id in context after authenticated request', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440100',
        'name' => 'Context User',
        'email' => 'contextuser@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)->get('/');

    expect(Context::get('user_id'))->toBe($user->id);
});

it('does not set context for unauthenticated request', function (): void {
    $this->get('/login');

    expect(Context::get('user_id'))->toBeNull();
});
