<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Query\GetAvailableModulesQuery;
use App\Domain\Authorization\Handler\Query\GetAvailableModulesHandler;

it('returns the configured modules', function (): void {
    $modules = [
        'users' => [
            'label' => 'Users',
            'features' => [
                'list' => [
                    'label' => 'User List',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
            ],
        ],
    ];

    $handler = new GetAvailableModulesHandler($modules);

    $result = $handler->handle(new GetAvailableModulesQuery);

    expect($result)->toBe($modules);
});

it('returns empty array when no modules configured', function (): void {
    $handler = new GetAvailableModulesHandler([]);

    $result = $handler->handle(new GetAvailableModulesQuery);

    expect($result)->toBe([]);
});
