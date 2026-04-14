<?php

declare(strict_types=1);

namespace App\Presentation\Support;

/**
 * Maps stack gap tokens to Tailwind gap-* classes.
 *
 * Keep in sync with resources/shared/ui-stack-gaps.json (Vue import). Covered by
 * {@see \Tests\Unit\Presentation\Support\StackGapClassMapTest}.
 */
final class StackGapClassMap
{
    /** @var array<string, string> */
    private const array STACK_GAP = [
        'none' => 'gap-0',
        'xs' => 'gap-3',
        'sm' => 'gap-4',
        'md' => 'gap-6',
        'default' => 'gap-8',
        'relaxed' => 'gap-10',
        'loose' => 'gap-12',
    ];

    public static function forGap(string $gap): string
    {
        return self::STACK_GAP[$gap] ?? self::STACK_GAP['default'];
    }
}
