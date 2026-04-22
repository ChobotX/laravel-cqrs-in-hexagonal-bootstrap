<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use Illuminate\View\View;

#[RequiresPermission('users.list.create')]
final readonly class ShowCreateUserController
{
    public function __invoke(): View
    {
        return view('users.create');
    }
}
