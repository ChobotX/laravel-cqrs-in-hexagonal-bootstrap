<?php

declare(strict_types=1);

use App\Infrastructure\Dev\DevSchemaResetter;
use Illuminate\Database\DatabaseManager;

it('calls DevSchemaResetter and outputs success', function (): void {
    $mock = Mockery::mock(Illuminate\Database\Connection::class);
    $mock->shouldReceive('select')->andReturn([]);
    $mock->shouldReceive('statement')->andReturn(true);

    $databaseManager = Mockery::mock(DatabaseManager::class);
    $databaseManager->shouldReceive('connection')->with('landlord')->andReturn($mock);
    $databaseManager->shouldReceive('purge')->with('landlord');

    $this->app->instance(DevSchemaResetter::class, new DevSchemaResetter($databaseManager));

    $this->artisan('tenant:drop-schemas')
        ->expectsOutput('All schemas dropped and landlord reset.')
        ->assertSuccessful();
});
