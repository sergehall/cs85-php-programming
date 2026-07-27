<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignments;

use App\Services\Modules\Module11A\Application\BrowseApiContacts;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BrowseModule11ApiDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:80'],
            'sort' => ['nullable', 'string', Rule::in(array_keys(BrowseApiContacts::SORTS))],
            'limit' => ['nullable', 'integer', Rule::in([4, 6, 10])],
            'refresh' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => trim((string) $this->query('search', '')),
            'sort' => (string) $this->query('sort', 'name_asc'),
            'limit' => (int) $this->query('limit', 10),
        ]);
    }
}
