<?php

declare(strict_types=1);

namespace App\Presentation\Http\Service;

use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\VersionStatus;

final readonly class VersionViewMapper
{
    private const array STATUS_CLASSES = [
        'active' => 'bg-green-50 text-green-700 ring-green-700/10',
        'draft' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
        'deprecated' => 'bg-gray-50 text-gray-500 ring-gray-500/10',
    ];

    private const array STATUS_LABELS = [
        'active' => 'messages.registry.versions.status_active',
        'draft' => 'messages.registry.versions.status_draft',
        'deprecated' => 'messages.registry.versions.status_deprecated',
    ];

    /**
     * @param  list<DefinitionVersion>  $versions
     * @return list<array{version: int, statusClasses: string, statusLabel: string, fieldCount: int, isDraft: bool, activateRoute: string|null}>
     */
    public static function mapForView(array $versions, string $namespace, string $slug): array
    {
        return array_map(
            fn (DefinitionVersion $definitionVersion): array => [
                'version' => $definitionVersion->version->value,
                'statusClasses' => self::STATUS_CLASSES[$definitionVersion->status->value],
                'statusLabel' => self::translateStatus($definitionVersion->status),
                'fieldCount' => count($definitionVersion->schema->fields),
                'isDraft' => $definitionVersion->status === VersionStatus::Draft,
                'activateRoute' => $definitionVersion->status === VersionStatus::Draft
                    ? route('registry.versions.activate', [$namespace, $slug, $definitionVersion->version->value])
                    : null,
            ],
            $versions,
        );
    }

    private static function translateStatus(VersionStatus $versionStatus): string
    {
        $translated = __(self::STATUS_LABELS[$versionStatus->value]);

        return is_string($translated) ? $translated : $versionStatus->value;
    }
}
