<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Contact;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')->ignore($contact instanceof Contact ? $contact->getKey() : null),
            ],
            'phone' => ['required', 'string', 'max:32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
        ]);
    }
}
