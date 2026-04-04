<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Team;

use App\Domain\Team\Command\CreateTeam\CreateTeamCommand;
use App\Domain\Team\Contract\TeamSlug;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateTeamRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:'.TeamSlug::MAX_LENGTH, 'regex:'.TeamSlug::SLUG_PATTERN, 'min:'.TeamSlug::MIN_LENGTH],
            'description' => ['nullable', 'string'],
            'parent_team_id' => ['nullable', 'uuid'],
        ];
    }

    public function toCommand(): CreateTeamCommand
    {
        $parentTeamId = $this->string('parent_team_id')->toString();

        return new CreateTeamCommand(
            id: Str::uuid()->toString(),
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            description: $this->string('description')->toString(),
            parentTeamId: $parentTeamId !== '' ? $parentTeamId : null,
        );
    }
}
