<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use Illuminate\View\View;

#[RequiresPermission('organizations.management.create')]
final readonly class ShowCreateOrganizationController
{
    public function __invoke(): View
    {
        return view('organizations.create');
    }
}
