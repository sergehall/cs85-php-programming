<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignments;

use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Foundation\Http\FormRequest;

final class DeleteModule9aContactByIdRequest extends FormRequest
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
        ];
    }
}
