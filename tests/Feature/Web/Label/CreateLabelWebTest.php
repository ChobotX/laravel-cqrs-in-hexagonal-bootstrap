<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Label\LabelModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createLabelsAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d100',
        'name' => 'Labels Create Admin',
        'email' => 'labels-create-admin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('requires authentication', function (): void {
    $this->postJson('/internal-api/labels', [
        'namespace' => 'users',
        'name' => 'Test',
    ])->assertUnauthorized();
});

it('requires labels.management.create permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d101',
        'name' => 'No Perms User',
        'email' => 'noperms-create-labels@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->postJson('/internal-api/labels', [
            'namespace' => 'users',
            'name' => 'Test',
        ])->assertForbidden();
});

it('creates label and returns 201 with data', function (): void {
    $userModel = createLabelsAdmin();

    $response = $this->actingAs($userModel)
        ->postJson('/internal-api/labels', [
            'namespace' => 'users',
            'name' => 'New Label',
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'name']])
        ->assertJsonPath('data.name', 'New Label');

    $this->assertDatabaseHas('labels', [
        'namespace' => 'users',
        'name' => 'New Label',
    ]);
});

it('returns existing label on duplicate', function (): void {
    $userModel = createLabelsAdmin();

    LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d110',
        'namespace' => 'users',
        'name' => 'Existing',
    ]);

    $response = $this->actingAs($userModel)
        ->postJson('/internal-api/labels', [
            'namespace' => 'users',
            'name' => 'Existing',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.id', '550e8400-e29b-41d4-a716-44665544d110')
        ->assertJsonPath('data.name', 'Existing');

    expect(LabelModel::where('namespace', 'users')->where('name', 'Existing')->count())->toBe(1);
});

it('validates required namespace field', function (): void {
    $userModel = createLabelsAdmin();

    $this->actingAs($userModel)
        ->postJson('/internal-api/labels', [
            'name' => 'Test',
        ])->assertUnprocessable()
        ->assertJsonValidationErrors(['namespace']);
});

it('validates required name field', function (): void {
    $userModel = createLabelsAdmin();

    $this->actingAs($userModel)
        ->postJson('/internal-api/labels', [
            'namespace' => 'users',
        ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});
