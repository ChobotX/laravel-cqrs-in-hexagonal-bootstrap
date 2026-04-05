<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\File\FileModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('shows profile page for authenticated user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d00',
        'name' => 'Profile User',
        'email' => 'profile@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profile User')
        ->assertSee(__('messages.profile.subtitle'));
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/profile')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/profile']));
});

it('updates name and password without special permissions', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d01',
        'name' => 'Old Name',
        'email' => 'basic@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'New Name',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and(Hash::check('new-password', $user->password))->toBeTrue();
});

it('keeps email unchanged without users.list.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d02',
        'name' => 'Keep Email',
        'email' => 'keep@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Updated Name',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('keep@example.com');
});

it('allows email change with users.list.update permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d03',
        'name' => 'Admin Profile',
        'email' => 'admin-profile@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Admin Profile',
            'email' => 'new-admin@example.com',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->email)->toBe('new-admin@example.com');
});

it('hides email field without users.list.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d04',
        'name' => 'No Email Edit',
        'email' => 'noedit@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->not->toContain('id="email"');
});

it('shows email field with users.list.update', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d05',
        'name' => 'Can Edit Email',
        'email' => 'canedit@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('id="email"');
});

it('admin changes password via profile', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d20',
        'name' => 'Admin Pass',
        'email' => 'adminpass@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Admin Pass',
            'email' => 'adminpass@example.com',
            'password' => 'new-secure-pass',
            'password_confirmation' => 'new-secure-pass',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect(Hash::check('new-secure-pass', $user->password))->toBeTrue();
});

it('does not show roles teams or permissions on profile', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d30',
        'name' => 'No Admin Fields',
        'email' => 'noadminfields@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $content = $response->getContent();
    expect($content)
        ->not->toContain('data-chip-selector')
        ->not->toContain('data-input-name="roles[]"')
        ->not->toContain('data-input-name="teams[]"')
        ->not->toContain(__('messages.permissions.effective_permissions'));
});

it('uploads avatar via profile', function (): void {
    Storage::fake('files');

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d31',
        'name' => 'Avatar Upload',
        'email' => 'avatar-upload@example.com',
        'password' => Hash::make('password123'),
    ]);

    $file = UploadedFile::fake()->image('avatar.png', 100, 100);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Avatar Upload',
            'avatar' => $file,
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->avatar_file_id)->not->toBeNull();

    $this->assertDatabaseHas('files', [
        'id' => $user->avatar_file_id,
        'namespace' => 'user-avatars',
    ]);
});

it('removes avatar via profile', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d32',
        'name' => 'Avatar Remove',
        'email' => 'avatar-remove@example.com',
        'password' => Hash::make('password123'),
    ]);

    $fileId = Str::uuid()->toString();
    FileModel::create([
        'id' => $fileId,
        'namespace' => 'user-avatars',
        'original_name' => 'old-avatar.png',
        'storage_path' => 'user-avatars/old.png',
        'mime_type' => 'image/png',
        'size_in_bytes' => 100,
        'version_number' => 1,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    $user->update(['avatar_file_id' => $fileId]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Avatar Remove',
            'remove_avatar' => '1',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->avatar_file_id)->toBeNull();
});

it('preserves avatar when no change submitted via profile', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d33',
        'name' => 'Avatar Preserve',
        'email' => 'avatar-preserve@example.com',
        'password' => Hash::make('password123'),
    ]);

    $fileId = Str::uuid()->toString();
    FileModel::create([
        'id' => $fileId,
        'namespace' => 'user-avatars',
        'original_name' => 'keep-avatar.png',
        'storage_path' => 'user-avatars/keep.png',
        'mime_type' => 'image/png',
        'size_in_bytes' => 100,
        'version_number' => 1,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    $user->update(['avatar_file_id' => $fileId]);

    $this->actingAs($user)
        ->put('/profile', [
            'name' => 'Avatar Preserve Updated',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect($user->avatar_file_id)->toBe($fileId);
});
