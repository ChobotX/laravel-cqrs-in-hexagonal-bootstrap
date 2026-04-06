<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

it('creates a user without password via invite flow', function (): void {
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
        ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);

    $created = UserModel::where('email', 'new@example.com')->first();
    expect($created->password)->toBeNull();
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
        ->assertSessionHasErrors(['name', 'email']);
});

it('validates unique email', function (): void {
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
            'email' => 'admin@example.com',
        ])->assertSessionHasErrors('email');
});

it('creates a user with avatar', function (): void {
    Storage::fake('files');

    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440034',
        'name' => 'Admin User',
        'email' => 'admin-avatar@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $this->actingAs($user)
        ->post('/users', [
            'name' => 'Avatar User',
            'email' => 'avatar-user@example.com',
            'avatar' => $file,
        ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $created = UserModel::where('email', 'avatar-user@example.com')->first();
    expect($created->avatar_file_id)->not->toBeNull();

    $this->assertDatabaseHas('files', [
        'id' => $created->avatar_file_id,
        'namespace' => 'user-avatars',
    ]);
});

it('creates a user without avatar', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440035',
        'name' => 'Admin User',
        'email' => 'admin-noavatar@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->post('/users', [
            'name' => 'No Avatar User',
            'email' => 'no-avatar@example.com',
        ])->assertRedirect('/users');

    $created = UserModel::where('email', 'no-avatar@example.com')->first();
    expect($created->avatar_file_id)->toBeNull();
});

it('redirects unauthenticated user', function (): void {
    $this->post('/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ])->assertRedirect('/login');
});
