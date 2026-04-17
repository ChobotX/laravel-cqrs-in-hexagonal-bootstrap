<?php

declare(strict_types=1);

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

it('throws when route parameter is not a string', function (): void {
    $request = new class extends FormRequest
    {
        use HandlesFormRequest;

        /** @return array<string, mixed> */
        public function rules(): array
        {
            return [];
        }
    };

    $request->routeString('userId');
})->throws(App\Presentation\Http\Request\Exception\InvalidRouteParameterException::class, 'Route parameter [userId] is not a string.');
