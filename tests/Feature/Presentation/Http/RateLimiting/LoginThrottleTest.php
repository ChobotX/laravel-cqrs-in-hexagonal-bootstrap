<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('allows login attempts within limit', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440900',
        'name' => 'Test User',
        'email' => 'throttle@example.com',
        'password' => Hash::make('password123'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => 'throttle@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect();
    }
});

it('blocks login after exceeding limit', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440901',
        'name' => 'Test User',
        'email' => 'throttle2@example.com',
        'password' => Hash::make('password123'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => 'throttle2@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $this->post('/login', [
        'email' => 'throttle2@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});
