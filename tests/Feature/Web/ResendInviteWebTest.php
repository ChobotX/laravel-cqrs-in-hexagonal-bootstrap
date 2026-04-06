<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

it('resends invite for non-activated user', function (): void {
    Mail::fake();

    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440740',
        'name' => 'Admin Resend',
        'email' => 'admin-resend@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440741',
        'name' => 'Pending User',
        'email' => 'pending@example.com',
        'password' => null,
    ]);

    $this->actingAs($admin)
        ->post('/users/550e8400-e29b-41d4-a716-446655440741/resend-invite')
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('redirects unauthenticated user for resend invite', function (): void {
    $this->post('/users/550e8400-e29b-41d4-a716-446655440742/resend-invite')
        ->assertRedirect('/login');
});

it('forbids user without permission to resend invite', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Reader Only',
        'Can only read users',
        ['users.list.read' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440743',
        'name' => 'No Perm User',
        'email' => 'noperm-resend@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440744',
        'name' => 'Target Pending',
        'email' => 'target-pending@example.com',
        'password' => null,
    ]);

    $this->actingAs($user)
        ->post('/users/550e8400-e29b-41d4-a716-446655440744/resend-invite')
        ->assertForbidden();
});
