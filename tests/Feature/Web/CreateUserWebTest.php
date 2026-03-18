<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows the create user form', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440030',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/users/create')
        ->assertStatus(200)
        ->assertSee('Create User');
});

it('creates a user with password', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440031',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->post('/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);

    $created = UserModel::where('email', 'new@example.com')->first();
    expect(Hash::check('secret1234', $created->password))->toBeTrue();
});

it('validates required fields', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440032',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->post('/users', [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

it('validates password confirmation', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440033',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->post('/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'mismatch',
        ])->assertSessionHasErrors('password');
});

it('redirects unauthenticated user', function (): void {
    $this->post('/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'secret1234',
        'password_confirmation' => 'secret1234',
    ])->assertRedirect('/login');
});
