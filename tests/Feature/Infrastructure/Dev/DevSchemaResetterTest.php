<?php

declare(strict_types=1);

use App\Infrastructure\Dev\DevSchemaResetter;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

it('drops tenant schemas and resets landlord', function (): void {
    $statements = [];

    $mock = Mockery::mock(Connection::class);
    $mock->shouldReceive('select')
        ->once()
        ->with("SELECT schema_name FROM information_schema.schemata WHERE schema_name LIKE 'tenant_%'")
        ->andReturn([
            (object) ['schema_name' => 'tenant_abc'],
            (object) ['schema_name' => 'tenant_xyz'],
        ]);
    $mock->shouldReceive('statement')
        ->times(4)
        ->andReturnUsing(function (string $sql) use (&$statements): bool {
            $statements[] = $sql;

            return true;
        });

    $databaseManager = Mockery::mock(DatabaseManager::class);
    $databaseManager->shouldReceive('connection')->with('landlord')->andReturn($mock);
    $databaseManager->shouldReceive('purge')->with('landlord')->once();

    $resetter = new DevSchemaResetter($databaseManager);
    $resetter->resetAll();

    expect($statements)->toBe([
        'DROP SCHEMA IF EXISTS "tenant_abc" CASCADE',
        'DROP SCHEMA IF EXISTS "tenant_xyz" CASCADE',
        'DROP SCHEMA IF EXISTS "landlord" CASCADE',
        'CREATE SCHEMA "landlord"',
    ]);
});

it('resets landlord when no tenant schemas exist', function (): void {
    $mock = Mockery::mock(Connection::class);
    $mock->shouldReceive('select')->once()->andReturn([]);
    $mock->shouldReceive('statement')->twice()->andReturn(true);

    $databaseManager = Mockery::mock(DatabaseManager::class);
    $databaseManager->shouldReceive('connection')->with('landlord')->andReturn($mock);
    $databaseManager->shouldReceive('purge')->with('landlord')->once();

    $resetter = new DevSchemaResetter($databaseManager);
    $resetter->resetAll();
});
