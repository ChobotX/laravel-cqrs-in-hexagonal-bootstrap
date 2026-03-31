<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('requires authentication', function (): void {
    $this->get('/notifications')
        ->assertRedirect();
});

it('renders notifications page for authenticated user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544f000',
        'name' => 'Notif Page User',
        'email' => 'notif-page@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->get('/notifications')
        ->assertOk()
        ->assertSee('app-notification-list');
});
