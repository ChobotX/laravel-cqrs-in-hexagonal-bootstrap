<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\File\FileModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('serves a file by id', function (): void {
    Storage::fake('files');
    Storage::disk('files')->put('user-avatars/test.png', 'png-content');

    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440400',
        'name' => 'File Viewer',
        'email' => 'file-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $fileId = Str::uuid()->toString();
    FileModel::create([
        'id' => $fileId,
        'namespace' => 'user-avatars',
        'original_name' => 'test.png',
        'storage_path' => 'user-avatars/test.png',
        'mime_type' => 'image/png',
        'size_in_bytes' => 11,
        'version_number' => 1,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/files/'.$fileId);

    $response->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('Content-Disposition', 'inline; filename="test.png"');

    expect($response->getContent())->toBe('png-content');
});

it('returns 404 for non-existent file', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440401',
        'name' => 'File 404',
        'email' => 'file-404@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/files/550e8400-e29b-41d4-a716-446655440999')
        ->assertStatus(404);
});

it('redirects unauthenticated user', function (): void {
    $this->get('/files/550e8400-e29b-41d4-a716-446655440999')
        ->assertRedirect();
});
