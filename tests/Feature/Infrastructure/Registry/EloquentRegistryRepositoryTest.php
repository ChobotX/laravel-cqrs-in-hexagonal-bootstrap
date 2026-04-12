<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Application\Pagination\Pagination;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;
use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionVersionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentEntryRepository;
use App\Infrastructure\Eloquent\Registry\EntryModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    if (! UserModel::query()->where('id', '550e8400-e29b-41d4-a716-446655440001')->exists()) {
        UserModel::create([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'name' => 'Registry Repo Test User',
            'email' => 'registry-repo-test@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    if (! UserModel::query()->where('id', '550e8400-e29b-41d4-a716-446655440e00')->exists()) {
        UserModel::create([
            'id' => '550e8400-e29b-41d4-a716-446655440e00',
            'name' => 'Registry Repo Test Owner',
            'email' => 'registry-repo-test-owner@test.com',
            'password' => Hash::make('password'),
        ]);
    }
});

function defRepo(): EloquentDefinitionRepository
{
    return app(EloquentDefinitionRepository::class);
}

function versionRepo(): EloquentDefinitionVersionRepository
{
    return app(EloquentDefinitionVersionRepository::class);
}

function entryRepo(): EloquentEntryRepository
{
    return app(EloquentEntryRepository::class);
}

function makeDef(string $id, string $ns, string $slug, string $name): Definition
{
    return new Definition(
        new DefinitionId($id),
        new DefinitionNamespace($ns),
        new DefinitionSlug($slug),
        new DefinitionName($name),
    );
}

// --- Definition Repository ---

it('returns null for non-existent definition by id', function (): void {
    expect(defRepo()->findById(new DefinitionId('550e8400-e29b-41d4-a716-446655440b00')))->toBeNull();
});

it('creates and finds a definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b01', 'enumerations', 'color', 'Color');
    defRepo()->create($definition);

    $found = defRepo()->findById(new DefinitionId('550e8400-e29b-41d4-a716-446655440b01'));
    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Color');
});

it('throws on duplicate definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b02', 'enumerations', 'dupe', 'Dupe');
    defRepo()->create($definition);
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b03', 'enumerations', 'dupe', 'Dupe2'));
})->throws(DefinitionAlreadyExistsException::class);

it('finds definition by namespace and slug', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b04', 'enumerations', 'size', 'Size');
    defRepo()->create($definition);

    $found = defRepo()->findByNamespaceAndSlug(new DefinitionNamespace('enumerations'), new DefinitionSlug('size'));
    expect($found)->not->toBeNull()
        ->and($found->id->value)->toBe('550e8400-e29b-41d4-a716-446655440b04');
});

it('returns null for non-existent namespace-slug', function (): void {
    expect(defRepo()->findByNamespaceAndSlug(new DefinitionNamespace('no'), new DefinitionSlug('such')))->toBeNull();
});

it('paginates definitions with namespace filter', function (): void {
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b05', 'cat_a', 'one', 'One'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b06', 'cat_b', 'two', 'Two'));

    $paginatedResult = defRepo()->allPaginated(new Pagination(1, 10), new DefinitionNamespace('cat_a'));
    expect($paginatedResult->total)->toBe(1)
        ->and($paginatedResult->items)->toHaveCount(1);
});

it('updates a definition name', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b07', 'enumerations', 'upd', 'Old');
    defRepo()->create($definition);

    $updated = new Definition($definition->id, $definition->namespace, $definition->slug, new DefinitionName('New'));
    defRepo()->update($updated);

    expect(defRepo()->findById($definition->id)->name->value)->toBe('New');
});

it('deletes a definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b08', 'enumerations', 'del', 'Del');
    defRepo()->create($definition);

    defRepo()->delete($definition->id);

    expect(defRepo()->findById($definition->id))->toBeNull();
});

it('returns unique sorted namespaces', function (): void {
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b09', 'zebra', 'z', 'Z'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b0a', 'alpha', 'a', 'A'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b0b', 'alpha', 'b', 'B'));

    expect(defRepo()->allNamespaces())->toBe(['alpha', 'zebra']);
});

// --- Definition Version Repository ---

it('returns null for non-existent version', function (): void {
    expect(versionRepo()->findById(new DefinitionVersionId('550e8400-e29b-41d4-a716-446655440c00')))->toBeNull();
});

it('creates a version and finds by definition and version number', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c10', 'namespace' => 'test', 'slug' => 'vertest', 'name' => 'VerTest']);

    $schema = new Schema([new StringField('x', 'X')]);
    $version = new DefinitionVersion(
        new DefinitionVersionId('550e8400-e29b-41d4-a716-446655440c11'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c10'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Draft,
    );

    versionRepo()->create($version);

    $found = versionRepo()->findByDefinitionAndVersion(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c10'),
        new VersionNumber(1),
    );
    expect($found)->not->toBeNull()
        ->and($found->status)->toBe(VersionStatus::Draft);
});

it('returns null for non-existent definition-version pair', function (): void {
    expect(versionRepo()->findByDefinitionAndVersion(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c99'),
        new VersionNumber(1),
    ))->toBeNull();
});

it('returns null when no active version exists', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c40', 'namespace' => 'test', 'slug' => 'noactive', 'name' => 'NoActive']);

    expect(versionRepo()->findActiveByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c40')))->toBeNull();
});

// --- Entry Repository ---

it('creates and finds an entry', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c20', 'namespace' => 'test', 'slug' => 'enttest', 'name' => 'EntTest']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c21',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c20',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => ['x' => ['type' => 'string']], 'required' => []],
        'status' => VersionStatus::Active,
    ]);

    $entry = new Entry(
        new EntryId('550e8400-e29b-41d4-a716-446655440c22'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c20'),
        new VersionNumber(1),
        new DefinitionNamespace('test'),
        new EntryTitle('Test Entry'),
        ['x' => 'val'],
        '550e8400-e29b-41d4-a716-446655440001',
    );

    entryRepo()->create($entry);

    $found = entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c22'));
    expect($found)->not->toBeNull()
        ->and($found->title->value)->toBe('Test Entry');
});

it('returns null for non-existent entry', function (): void {
    expect(entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c99')))->toBeNull();
});

it('silently returns when updating non-existent entry', function (): void {
    $entry = new Entry(
        new EntryId('550e8400-e29b-41d4-a716-446655440c99'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c20'),
        new VersionNumber(1),
        new DefinitionNamespace('test'),
        new EntryTitle('Ghost'),
        ['x' => 'val'],
        '550e8400-e29b-41d4-a716-446655440001',
    );

    entryRepo()->update($entry);

    expect(entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c99')))->toBeNull();
});

it('paginates entries with default sort when no sortings provided', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c50', 'namespace' => 'test', 'slug' => 'defsort', 'name' => 'DefSort']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c51',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c50',
        'version' => 1,
        'body' => [['name' => 'x', 'label' => 'X', 'type' => 'string', 'required' => false]],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c52',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c50',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Zebra',
        'data' => ['x' => 'z'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c53',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c50',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Alpha',
        'data' => ['x' => 'a'],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c50'),
        new Pagination(1, 15),
    );

    expect($paginatedResult->total)->toBe(2)
        ->and($paginatedResult->items[0]->title->value)->toBe('Alpha')
        ->and($paginatedResult->items[1]->title->value)->toBe('Zebra');
});

it('checks if entries exist by definition', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c30', 'namespace' => 'test', 'slug' => 'exists', 'name' => 'Exists']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c31',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c30',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);

    expect(entryRepo()->existsByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c30')))->toBeFalse();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c32',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c30',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'X',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    expect(entryRepo()->existsByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c30')))->toBeTrue();
});

// --- ScopesOwnedQuery trait via EloquentEntryRepository ---

it('returns all entries when accessContext is null (no filter)', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d00', 'namespace' => 'test', 'slug' => 'scope-null', 'name' => 'ScopeNull']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d01',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d00',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d02',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d00',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Entry A',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d03',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d00',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Entry B',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d00'),
        new Pagination(1, 15),
        accessContext: null,
    );

    expect($paginatedResult->total)->toBe(2);
});

it('returns all entries when scope is All (no filter)', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d10', 'namespace' => 'test', 'slug' => 'scope-all', 'name' => 'ScopeAll']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d11',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d10',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d12',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d10',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Entry C',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);

    $context = new AccessContext(AccessScope::All, null);
    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d10'),
        new Pagination(1, 15),
        accessContext: $context,
    );

    expect($paginatedResult->total)->toBe(1);
});

it('filters entries by owner with Own scope', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d20', 'namespace' => 'test', 'slug' => 'scope-own', 'name' => 'ScopeOwn']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d21',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d20',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d22',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d20',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'My Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d23',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d20',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Other Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    $context = new AccessContext(AccessScope::Own, ['550e8400-e29b-41d4-a716-446655440001'], []);
    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d20'),
        new Pagination(1, 15),
        accessContext: $context,
    );

    expect($paginatedResult->total)->toBe(1)
        ->and($paginatedResult->items[0]->title->value)->toBe('My Entry');
});

it('includes shared entries via Own scope sharedResourceIds', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d30', 'namespace' => 'test', 'slug' => 'scope-shared', 'name' => 'ScopeShared']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d31',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d30',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d32',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d30',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'My Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d33',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d30',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Shared Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    // User 001 owns one, and has shared access to d33
    $context = new AccessContext(
        AccessScope::Own,
        ['550e8400-e29b-41d4-a716-446655440001'],
        ['550e8400-e29b-41d4-a716-446655440d33'],
    );
    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d30'),
        new Pagination(1, 15),
        accessContext: $context,
    );

    expect($paginatedResult->total)->toBe(2);
});

it('returns no entries when Own scope with empty visibleIds and empty sharedIds', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d40', 'namespace' => 'test', 'slug' => 'scope-empty', 'name' => 'ScopeEmpty']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d41',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d40',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d42',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d40',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Invisible Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    $context = new AccessContext(AccessScope::Own, [], []);
    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d40'),
        new Pagination(1, 15),
        accessContext: $context,
    );

    expect($paginatedResult->total)->toBe(0);
});

it('filters entries by team scope with visibleIds', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440d50', 'namespace' => 'test', 'slug' => 'scope-team', 'name' => 'ScopeTeam']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d51',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d50',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d52',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d50',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Team Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);
    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d53',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440d50',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'Outside Team Entry',
        'data' => [],
        'created_by_user_id' => '550e8400-e29b-41d4-a716-446655440e00',
    ]);

    // Team scope with only user 001 visible
    $context = new AccessContext(AccessScope::Team, ['550e8400-e29b-41d4-a716-446655440001'], []);
    $paginatedResult = entryRepo()->findByDefinitionPaginated(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440d50'),
        new Pagination(1, 15),
        accessContext: $context,
    );

    expect($paginatedResult->total)->toBe(1)
        ->and($paginatedResult->items[0]->title->value)->toBe('Team Entry');
});
