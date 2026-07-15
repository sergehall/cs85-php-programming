<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignments;

use App\Models\Contact;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateModule9aContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(Module9aWriteAccess::class)->allows($this->user());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $contact = $this->route('contact');

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')->ignore($contact instanceof Contact ? $contact->getKey() : null),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:150'],
            'contact_group_id' => ['nullable', 'integer', 'exists:contact_groups,id'],
            'role' => ['required', Rule::in([Contact::ROLE_USER, Contact::ROLE_ADMIN])],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
