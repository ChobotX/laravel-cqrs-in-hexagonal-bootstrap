<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Exception\TenantLogoStorageException;
use App\Infrastructure\Filesystem\TenantLogoFileStorage;
use Illuminate\Contracts\Filesystem\Filesystem;

it('throws when filesystem store fails', function (): void {
    $mock = Mockery::mock(Filesystem::class);
    $mock->shouldReceive('putFileAs')
        ->once()
        ->andReturn(false);

    $storage = new TenantLogoFileStorage($mock);

    $storage->store('tenant-123', '/tmp/logo.png', 'png');
})->throws(TenantLogoStorageException::class);
