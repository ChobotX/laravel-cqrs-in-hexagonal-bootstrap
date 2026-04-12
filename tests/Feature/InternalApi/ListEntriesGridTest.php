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
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
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
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d311',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Honda',
        'data' => ['brand' => 'Honda'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
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
            'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
        ]);
    }

    $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?page=2&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.total', 20);
});

it('sorts entries by title ascending', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d340',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Zebra',
        'data' => ['brand' => 'Zebra'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d341',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Alpha',
        'data' => ['brand' => 'Alpha'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?sort=title&direction=asc')
        ->assertOk();

    $titles = array_column($response->json('data'), 'title');
    expect($titles)->toBe(['Alpha', 'Zebra']);
});

it('sorts entries by title descending', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d350',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Alpha',
        'data' => ['brand' => 'Alpha'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d351',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Zebra',
        'data' => ['brand' => 'Zebra'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?sort=title&direction=desc')
        ->assertOk();

    $titles = array_column($response->json('data'), 'title');
    expect($titles)->toBe(['Zebra', 'Alpha']);
});

it('sorts entries by version', function (): void {
    $userModel = entriesGridAdmin();
    $definitionModel = createTestDefinition();

    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d201',
        'definition_id' => $definitionModel->id,
        'version' => 2,
        'body' => [
            ['name' => 'brand', 'label' => 'Brand', 'type' => 'string', 'required' => true],
        ],
        'status' => 'active',
    ]);

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d360',
        'definition_id' => $definitionModel->id,
        'definition_version' => 2,
        'namespace' => 'enumerations',
        'title' => 'BMW',
        'data' => ['brand' => 'BMW'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544d361',
        'definition_id' => $definitionModel->id,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Toyota',
        'data' => ['brand' => 'Toyota'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list?sort=version&direction=desc')
        ->assertOk();

    $versions = array_column($response->json('data'), 'version');
    expect($versions)->toBe([2, 1]);
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
        'created_by_user_id' => '550e8400-e29b-41d4-a716-44665544d000',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/car_brand/entries/list')
        ->assertOk();

    expect($response->json('data.0.version'))->toBe(1);
});
