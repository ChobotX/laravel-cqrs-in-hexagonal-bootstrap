<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

final readonly class SidebarNavItemViewData
{
    public function __construct(
        public string $href,
        public string $icon,
        public string $label,
        public bool $active,
    ) {}
}
