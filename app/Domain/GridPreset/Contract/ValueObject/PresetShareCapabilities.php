<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\ValueObject;

/**
 * Contract-level value object for preset share capabilities used across GridPreset commands, queries, and events.
 */
final readonly class PresetShareCapabilities
{
    /**
     * @param  list<array{id: string, name: string}>  $shareableTeams
     */
    public function __construct(
        /** Boolean flag for this state or capability. */
        public bool $canShareTeam,
        /** Boolean flag for this state or capability. */
        public bool $canShareGlobal,
        /** Array for `shareableTeams`; see constructor PHPDoc for structural tags when present. */
        public array $shareableTeams,
    ) {}
}
