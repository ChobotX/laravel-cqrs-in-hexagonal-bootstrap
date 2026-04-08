<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\ValueObject;

final readonly class PresetShareCapabilities
{
    /**
     * @param  list<array{id: string, name: string}>  $shareableTeams
     */
    public function __construct(
        public bool $canShareTeam,
        public bool $canShareGlobal,
        public array $shareableTeams,
    ) {}
}
