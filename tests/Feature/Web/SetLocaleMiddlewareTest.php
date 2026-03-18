<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('sets app locale from session', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440610',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->withSession(['locale' => 'cs'])
        ->actingAs($user)
        ->get('/users')
        ->assertSuccessful();

    $this->assertSame('cs', app()->getLocale());
});

it('keeps default locale without session', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440611',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users')
        ->assertSuccessful();

    $this->assertSame('en', app()->getLocale());
});
