<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignments;

use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateModule9aContactDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(Module9aWriteAccess::class)->allows($this->user());
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'contact_id' => ['required', 'integer', 'min:1', 'exists:contacts,id'],
            'details_phone' => ['required', 'string', 'max:32'],
            'details_company' => ['present', 'nullable', 'string', 'max:150'],
            'details_contact_group_id' => ['present', 'nullable', 'integer', 'exists:contact_groups,id'],
        ];
    }
}
