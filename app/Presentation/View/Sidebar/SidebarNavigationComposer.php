<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

use Illuminate\Contracts\View\View;

final readonly class SidebarNavigationComposer
{
    public function __construct(
        private SidebarNavigationBuilder $sidebarNavigationBuilder,
    ) {}

    public function compose(View $view): void
    {
        $view->with('sidebarNav', $this->sidebarNavigationBuilder->build());
    }
}
