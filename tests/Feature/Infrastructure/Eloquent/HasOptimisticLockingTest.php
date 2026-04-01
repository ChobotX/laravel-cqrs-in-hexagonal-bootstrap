<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Infrastructure\Eloquent\ConcurrentModificationException;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    Schema::create('optimistic_test_models', function ($table): void {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->integer('version')->default(1);
        $table->timestamps();
    });
});

afterEach(function (): void {
    Schema::dropIfExists('optimistic_test_models');
});

function createOptimisticModel(string $id, string $name): OptimisticTestModel
{
    $model = new OptimisticTestModel;
    $model->id = $id;
    $model->name = $name;
    $model->save();

    return $model;
}

function dbVersion(string $id): int
{
    /** @var int $version */
    $version = DB::table('optimistic_test_models')->where('id', $id)->value('version');

    return $version;
}

it('sets version to 1 on creation', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440099', 'Test');

    expect(dbVersion($optimisticTestModel->getKey()))->toBe(1);
});

it('increments version on update', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440098', 'Original');

    $optimisticTestModel->name = 'Updated';
    $optimisticTestModel->save();

    expect(dbVersion($optimisticTestModel->getKey()))->toBe(2);
});

it('throws ConcurrentModificationException on stale update', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440097', 'Original');

    DB::table('optimistic_test_models')
        ->where('id', $optimisticTestModel->getKey())
        ->update(['version' => 5, 'name' => 'Changed by another']);

    $optimisticTestModel->name = 'Stale update';
    $optimisticTestModel->save();
})->throws(ConcurrentModificationException::class);

it('allows sequential updates', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440096', 'V1');

    $optimisticTestModel->name = 'V2';
    $optimisticTestModel->save();

    $optimisticTestModel->name = 'V3';
    $optimisticTestModel->save();

    expect(dbVersion($optimisticTestModel->getKey()))->toBe(3);
});

it('aborts update when updating event is cancelled', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440094', 'Original');

    OptimisticTestModel::updating(fn (): bool => false);

    $optimisticTestModel->name = 'Blocked';
    $result = $optimisticTestModel->save();

    expect($result)->toBeFalse()
        ->and(dbVersion($optimisticTestModel->getKey()))->toBe(1);
});

it('exception has correct status code and user message', function (): void {
    $exception = new ConcurrentModificationException('TestModel', 'abc-123');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->statusCode())->toBe(409)
        ->and($exception->userMessage($translator))->toBe('messages.exceptions.concurrent_modification')
        ->and($exception->modelClass)->toBe('TestModel')
        ->and($exception->modelId)->toBe('abc-123');
});

it('does not increment version when nothing changed', function (): void {
    $optimisticTestModel = createOptimisticModel('550e8400-e29b-41d4-a716-446655440095', 'Same');

    $optimisticTestModel->save();

    expect(dbVersion($optimisticTestModel->getKey()))->toBe(1);
});

final class OptimisticTestModel extends Model
{
    use HasOptimisticLocking;

    public $incrementing = false;

    protected $table = 'optimistic_test_models';

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'version'];
}
