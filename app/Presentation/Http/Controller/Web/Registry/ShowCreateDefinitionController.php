<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Contract\Attribute\RequiresPermission;
use Illuminate\View\View;

#[RequiresPermission('registry.definitions.create')]
final readonly class ShowCreateDefinitionController
{
    public function __invoke(): View
    {
        return view('registry.definitions.create');
    }
}
