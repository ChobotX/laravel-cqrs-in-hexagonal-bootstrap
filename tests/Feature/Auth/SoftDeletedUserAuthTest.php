<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('denies web login for soft-deleted user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440800',
        'name' => 'Deleted User',
        'email' => 'deleted@example.com',
        'password' => Hash::make('password123'),
    ]);

    $user->delete();

    $response = $this->from('/login')->post('/login', [
        'email' => 'deleted@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});

it('denies API access for soft-deleted user with token', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440801',
        'name' => 'Deleted API User',
        'email' => 'deleted-api@example.com',
        'password' => Hash::make('password123'),
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $user->delete();

    $this->getJson('/api/users/'.$user->id, [
        'Authorization' => 'Bearer '.$token,
    ])->assertUnauthorized();
});

it('allows web login for active user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440802',
        'name' => 'Active User',
        'email' => 'active@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => 'active@example.com',
        'password' => 'password123',
    ])->assertRedirect('/users');

    $this->assertAuthenticated();
});
