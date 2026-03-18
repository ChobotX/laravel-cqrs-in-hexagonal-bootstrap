<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('soft deletes a user', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440050',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440051',
        'name' => 'Delete Me',
        'email' => 'delete@example.com',
    ]);

    $this->actingAs($admin)
        ->delete('/users/550e8400-e29b-41d4-a716-446655440051')
        ->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertSoftDeleted('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440051',
    ]);
});

it('redirects unauthenticated user', function (): void {
    $this->delete('/users/550e8400-e29b-41d4-a716-446655440051')
        ->assertRedirect('/login');
});

it('returns 404 for missing user', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440052',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $this->actingAs($admin)
        ->delete('/users/550e8400-e29b-41d4-a716-446655440099')
        ->assertStatus(404);
});
