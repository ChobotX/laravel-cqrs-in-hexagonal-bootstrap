<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ArtisanContract;

it('runs setup successfully', function (): void {
    $realKernel = app(ArtisanContract::class);

    $legacyMock = Mockery::mock($realKernel)->makePartial();

    $legacyMock->shouldReceive('call')
        ->with('migrate', Mockery::type('array'))
        ->once()
        ->andReturn(0);

    $legacyMock->shouldReceive('call')
        ->with('db:seed', Mockery::type('array'))
        ->once()
        ->andReturn(0);

    $legacyMock->shouldReceive('output')
        ->andReturn('');

    $this->app->instance(ArtisanContract::class, $legacyMock);

    $this->artisan('tenant:setup')
        ->assertSuccessful();
});
