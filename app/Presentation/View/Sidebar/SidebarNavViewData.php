<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

final readonly class SidebarNavViewData
{
    /**
     * @param  list<SidebarNavSectionViewData>  $sections
     */
    public function __construct(
        public SidebarNavItemViewData $dashboard,
        public array $sections,
    ) {}
}
