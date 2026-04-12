<?php

declare(strict_types=1);

namespace App\Application\Event;

final readonly class PropertyChangeBuilder
{
    /**
     * @param  array<string, array{0: string|bool|int|null, 1: string|bool|int|null}>  $pairs
     * @return list<PropertyChange>
     */
    public function diff(array $pairs): array
    {
        $changes = [];

        foreach ($pairs as $property => [$old, $new]) {
            if ($old === $new) {
                continue;
            }

            $changes[] = new PropertyChange(property: $property, old: $old, new: $new);
        }

        return $changes;
    }
}
