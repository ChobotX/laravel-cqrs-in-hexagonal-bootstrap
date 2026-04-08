<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EntryModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function entriesGridAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d000',
        'name' => 'Entries Admin',
        'email' => 'entries-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

function createTestDefinition(): DefinitionModel
{
    $definition = DefinitionModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d100',
        'namespace' => 'enumerations',
        'slug' => 'car_brand',
        'name' => 'Car Brand',
    ]);

    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d200',
        'definition_id' => $definition->id,
        'version' => 1,
        'body' => [
            ['name' => 'brand', 'label' => 'Brand', 'type' => 'string', 'required' => true],
        ],
        'status' => 'active',
    ]);

    return $definition;
}

it('returns entries as json', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d300',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Toyota',
        'data' => ['brand' => 'Toyota'],
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Toyota')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonStructure([
            'data' => [['id', 'title', 'version', 'edit_url', 'delete_url']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
        ]);
});

it('redirects unauthenticated user', function (): void {
    $this->getJson('/internal-api/registry/enumerations/car_brand/entries/list')
        ->assertUnauthorized();
});

it('returns 404 for unknown definition', function (): void {
    $userModel = entriesGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/registry/unknown/slug/entries/list')
        ->assertNotFound();
});

it('filters entries by search', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d310',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Toyota',
        'data' => ['brand' => 'Toyota'],
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d311',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Honda',
        'data' => ['brand' => 'Honda'],
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?search=Toyota')
        ->assertOk();

    $titles = array_column($response->json('data'), 'title');
    expect($titles)->toContain('Toyota');
    expect($titles)->not->toContain('Honda');
});

it('paginates results', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    for ($i = 1; $i <= 20; $i++) {
        EntryModel::create([
            'id' => sprintf('550e8400-e29b-41d4-a716-44665544e%03d', $i),
            'definition_id' => $definitionModel->id,
            'definition_version' => 1,
            'namespace' => 'enumerations',
            'title' => 'Entry '.$i,
            'data' => ['brand' => 'Brand '.$i],
        ]);
    }

    $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?page=2&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.total', 20);
});

it('includes version in entry data', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d320',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'BMW',
        'data' => ['brand' => 'BMW'],
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list')
        ->assertOk();

    expect($response->json('data.0.version'))->toBe(1);
});
