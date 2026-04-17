<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Team;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

use function array_column;
use function implode;

final class ManageTeamMembersRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            '_action' => ['required', 'in:'.implode(',', array_column(TeamMemberAction::cases(), 'value'))],
            'user_id' => ['required', 'uuid'],
        ];
    }

    public function action(): TeamMemberAction
    {
        return TeamMemberAction::from($this->string('_action')->toString());
    }

    public function userId(): string
    {
        return $this->string('user_id')->toString();
    }
}
