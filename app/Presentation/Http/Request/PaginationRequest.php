<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request;

use App\Application\Pagination\Pagination;
use Illuminate\Foundation\Http\FormRequest;

final class PaginationRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:'.Pagination::MAX_PER_PAGE],
        ];
    }

    public function pagination(): Pagination
    {
        return new Pagination(
            $this->integer('page', 1),
            $this->integer('per_page', Pagination::DEFAULT_PER_PAGE),
        );
    }
}
