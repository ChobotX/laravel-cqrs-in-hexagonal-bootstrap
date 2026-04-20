<?php

declare(strict_types=1);

return [
    'cache_ttl' => env('FEATURE_FLAG_CACHE_TTL', 300),
    'groups' => [
        'registry' => [
            'label' => 'messages.feature_flags.groups.registry.label',
            'flags' => [
                'schema-builder' => [
                    'type' => 'boolean',
                    'default' => true,
                    'label' => 'messages.feature_flags.groups.registry.flags.schema-builder.label',
                    'description' => 'messages.feature_flags.groups.registry.flags.schema-builder.description',
                ],
            ],
        ],
        'sso' => [
            'label' => 'messages.feature_flags.groups.sso.label',
            'flags' => [
                'enabled' => [
                    'type' => 'boolean',
                    'default' => false,
                    'label' => 'messages.feature_flags.groups.sso.flags.enabled.label',
                    'description' => 'messages.feature_flags.groups.sso.flags.enabled.description',
                ],
            ],
        ],
    ],
];
