<?php

declare(strict_types=1);

namespace App\Services\Modules\Module9A;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;

final class ContactDatasetImporter
{
    public function sourcePath(): string
    {
        return base_path('assignments/module9a/data/contacts.json');
    }

    public function sourceLabel(): string
    {
        return 'assignments/module9a/data/contacts.json';
    }

    public function sourceJson(): string
    {
        $json = file_get_contents($this->sourcePath());

        if (! is_string($json)) {
            throw new RuntimeException('The Module 9 default contact dataset could not be read.');
        }

        return $json;
    }

    /**
     * @return array{groups: int, contacts: int, created: int, updated: int}
     */
    public function import(): array
    {
        $dataset = $this->validatedDataset();

        return DB::transaction(function () use ($dataset): array {
            $groupIds = [];

            foreach ($dataset['groups'] as $groupData) {
                $group = ContactGroup::query()->updateOrCreate(
                    ['name' => $groupData['name']],
                    ['description' => $groupData['description'] ?? null],
                );

                $groupIds[$groupData['key']] = $group->getKey();
            }

            $created = 0;
            $updated = 0;

            foreach ($dataset['contacts'] as $contactData) {
                $groupId = $groupIds[$contactData['group']] ?? null;

                if (! is_int($groupId)) {
                    throw new RuntimeException("The contact dataset references an unknown group [{$contactData['group']}].");
                }

                $contact = Contact::query()->updateOrCreate(
                    ['email' => mb_strtolower(trim($contactData['email']))],
                    [
                        'contact_group_id' => $groupId,
                        'first_name' => trim($contactData['first_name']),
                        'last_name' => trim($contactData['last_name']),
                        'phone' => $contactData['phone'] ?? null,
                        'company' => $contactData['company'] ?? null,
                        'role' => $contactData['role'],
                        'is_active' => $contactData['is_active'],
                        'notes' => $contactData['notes'] ?? null,
                    ],
                );

                $contact->wasRecentlyCreated ? $created++ : $updated++;
            }

            return [
                'groups' => count($dataset['groups']),
                'contacts' => count($dataset['contacts']),
                'created' => $created,
                'updated' => $updated,
            ];
        });
    }

    /**
     * @return array{contacts: int, groups: int}
     */
    public function clear(): array
    {
        return DB::transaction(function (): array {
            $contacts = Contact::query()->delete();
            $groups = ContactGroup::query()->delete();

            return ['contacts' => $contacts, 'groups' => $groups];
        });
    }

    /**
     * @return array{
     *   version: int,
     *   groups: list<array{key: string, name: string, description?: string|null}>,
     *   contacts: list<array{first_name: string, last_name: string, email: string, phone?: string|null, company?: string|null, group: string, role: string, is_active: bool, notes?: string|null}>
     * }
     */
    private function validatedDataset(): array
    {
        try {
            $decoded = json_decode($this->sourceJson(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('The Module 9 default contact dataset is not valid JSON.', previous: $exception);
        }

        if (! is_array($decoded)) {
            throw new RuntimeException('The Module 9 default contact dataset must be a JSON object.');
        }

        $validator = Validator::make($decoded, [
            'version' => ['required', 'integer', 'min:1'],
            'groups' => ['required', 'array', 'min:1'],
            'groups.*.key' => ['required', 'string', 'alpha_dash', 'max:50', 'distinct'],
            'groups.*.name' => ['required', 'string', 'max:80', 'distinct'],
            'groups.*.description' => ['nullable', 'string', 'max:255'],
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.first_name' => ['required', 'string', 'max:100'],
            'contacts.*.last_name' => ['required', 'string', 'max:100'],
            'contacts.*.email' => ['required', 'email', 'max:255', 'distinct'],
            'contacts.*.phone' => ['nullable', 'string', 'max:32'],
            'contacts.*.company' => ['nullable', 'string', 'max:150'],
            'contacts.*.group' => ['required', 'string', 'max:50'],
            'contacts.*.role' => ['required', Rule::in([Contact::ROLE_USER, Contact::ROLE_ADMIN])],
            'contacts.*.is_active' => ['required', 'boolean'],
            'contacts.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            /** @var array{version: int, groups: list<array{key: string, name: string, description?: string|null}>, contacts: list<array{first_name: string, last_name: string, email: string, phone?: string|null, company?: string|null, group: string, role: string, is_active: bool, notes?: string|null}>} $validated */
            $validated = $validator->validate();
        } catch (ValidationException $exception) {
            throw new RuntimeException('The Module 9 default contact dataset does not match its expected schema.', previous: $exception);
        }

        $groupKeys = array_column($validated['groups'], 'key');

        foreach ($validated['contacts'] as $contact) {
            if (! in_array($contact['group'], $groupKeys, true)) {
                throw new RuntimeException("The contact dataset references an unknown group [{$contact['group']}].");
            }
        }

        return $validated;
    }
}
