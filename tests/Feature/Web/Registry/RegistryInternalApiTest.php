<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EntryModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function internalApiUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a00',
        'name' => 'API User',
        'email' => 'apiuser@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

function seedApiDefinitionWithEntries(): void
{
    DefinitionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a10',
        'namespace' => 'enumerations',
        'slug' => 'brand',
        'name' => 'Brand',
    ]);

    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a11',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440a10',
        'version' => 1,
        'body' => [
            'type' => 'object',
            'properties' => ['name' => ['type' => 'string']],
            'required' => [],
        ],
        'status' => VersionStatus::Active,
    ]);

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a20',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440a10',
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => 'Acme Corp',
        'data' => ['name' => 'Acme'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440a00',
    ]);
}

it('returns entries as JSON for internal API', function (): void {
    $userModel = internalApiUser();
    seedApiDefinitionWithEntries();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/brand/entries')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'title']]]);

    /** @var list<array{id: string, title: string}> $data */
    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['title'])->toBe('Acme Corp');
});

it('returns definitions list as JSON', function (): void {
    $userModel = internalApiUser();

    DefinitionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a40',
        'namespace' => 'enumerations',
        'slug' => 'color',
        'name' => 'Color',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/definitions')
        ->assertOk()
        ->assertJsonStructure(['data' => [['namespace', 'slug', 'name']]]);

    /** @var list<array{namespace: string, slug: string, name: string}> $data */
    $data = $response->json('data');
    expect($data)->not->toBeEmpty()
        ->and($data[0]['slug'])->toBe('color');
});

it('returns unique namespaces as JSON', function (): void {
    $userModel = internalApiUser();

    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440a50', 'namespace' => 'enumerations', 'slug' => 'a', 'name' => 'A']);
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440a51', 'namespace' => 'system', 'slug' => 'b', 'name' => 'B']);
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440a52', 'namespace' => 'enumerations', 'slug' => 'c', 'name' => 'C']);

    /** @var list<string> $data */
    $data = $this->actingAs($userModel)
        ->getJson('/internal-api/registry/namespaces')
        ->assertOk()
        ->json('data');

    expect($data)->toBe(['enumerations', 'system']);
});

it('returns empty data for definition with no entries', function (): void {
    $userModel = internalApiUser();

    DefinitionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a30',
        'namespace' => 'enumerations',
        'slug' => 'empty_def',
        'name' => 'Empty',
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/registry/enumerations/empty_def/entries')
        ->assertOk()
        ->assertJson(['data' => []]);
});
