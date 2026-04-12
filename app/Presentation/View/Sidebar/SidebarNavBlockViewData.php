<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

final readonly class SidebarNavBlockViewData
{
    /**
     * @param  list<SidebarNavItemViewData>  $items
     */
    public function __construct(
        public string $id,
        public string $label,
        public bool $collapsible,
        public bool $open,
        public array $items,
        public ?string $groupIcon,
    ) {}
}
