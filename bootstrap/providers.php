<?php

declare(strict_types=1);

use App\Infrastructure\Provider\AuthorizationServiceProvider;
use App\Infrastructure\Provider\AuthServiceProvider;
use App\Infrastructure\Provider\BusServiceProvider;
use App\Infrastructure\Provider\RepositoryServiceProvider;
use App\Infrastructure\Provider\TracingServiceProvider;

return [
    AuthServiceProvider::class,
    AuthorizationServiceProvider::class,
    BusServiceProvider::class,
    RepositoryServiceProvider::class,
    TracingServiceProvider::class,
];
