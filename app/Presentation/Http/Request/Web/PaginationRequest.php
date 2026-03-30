<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web;

use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Presentation\Http\Request\FormRequest;

final class PaginationRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:'.Pagination::MAX_PER_PAGE],
            'sort' => ['sometimes', 'string', 'max:50', 'regex:/^[a-z_]+$/'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    public function pagination(): Pagination
    {
        return new Pagination(
            $this->integer('page', 1),
            $this->integer('per_page', Pagination::DEFAULT_PER_PAGE),
        );
    }

    public function sorting(): ?Sorting
    {
        $sort = $this->string('sort')->toString();

        if ($sort === '') {
            return null;
        }

        return new Sorting(
            $sort,
            SortDirection::tryFrom($this->string('direction', 'asc')->toString()) ?? SortDirection::Asc,
        );
    }
}
