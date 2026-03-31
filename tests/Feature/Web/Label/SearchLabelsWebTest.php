<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Label\LabelModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function searchLabelsAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d000',
        'name' => 'Labels Search Admin',
        'email' => 'labels-search-admin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d010',
        'namespace' => 'users',
        'name' => 'VIP',
    ]);

    LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d011',
        'namespace' => 'users',
        'name' => 'Internal',
    ]);

    LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d012',
        'namespace' => 'teams',
        'name' => 'Engineering',
    ]);

    return $admin;
}

it('requires authentication', function (): void {
    $this->getJson('/internal-api/labels/search?namespace=users&q=test')
        ->assertUnauthorized();
});

it('requires labels.management.read permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d001',
        'name' => 'No Perms User',
        'email' => 'noperms-labels@test.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->getJson('/internal-api/labels/search?namespace=users&q=test')
        ->assertForbidden();
});

it('returns matching labels filtered by namespace', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=users&q=VIP')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'VIP');
});

it('does not return labels from other namespaces', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=users&q=Engineering')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('returns all labels in namespace with empty search term', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=users')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('respects exclude parameter', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=users&exclude[]=550e8400-e29b-41d4-a716-44665544d010')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Internal');
});

it('returns empty for unknown namespace', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=nonexistent')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('returns correct JSON structure', function (): void {
    $userModel = searchLabelsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/labels/search?namespace=users&q=VIP')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name']]]);
});
