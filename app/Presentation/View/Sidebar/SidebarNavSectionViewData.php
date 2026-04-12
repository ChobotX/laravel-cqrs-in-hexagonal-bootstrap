<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

final readonly class SidebarNavSectionViewData
{
    /**
     * @param  list<SidebarNavBlockViewData>  $blocks
     */
    public function __construct(
        public string $label,
        public array $blocks,
    ) {}
}
