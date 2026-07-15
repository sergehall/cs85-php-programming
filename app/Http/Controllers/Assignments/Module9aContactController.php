<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignments\DeleteModule9aContactByIdRequest;
use App\Http\Requests\Assignments\StoreModule9aContactRequest;
use App\Http\Requests\Assignments\UpdateModule9aContactDetailsRequest;
use App\Http\Requests\Assignments\UpdateModule9aContactRequest;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Services\Modules\Module9A\ContactDatasetImporter;
use App\Services\Modules\Module9A\ContactDirectoryQuery;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class Module9aContactController extends Controller
{
    public function index(
        Request $request,
        ContactDirectoryQuery $directory,
        ContactDatasetImporter $dataset,
        Module9aWriteAccess $writeAccess,
    ): View {
        $filters = $directory->filters($request);
        $contacts = $directory->build($filters)->get();
        $groups = ContactGroup::query()->orderBy('name')->get();
        $contactOptions = Contact::query()
            ->with('group')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $editId = filter_var($request->query('edit'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $editingContact = $editId === false
            ? null
            : Contact::query()->with('group')->find($editId);

        return view('assignments.module9a.contacts', [
            'canMutate' => $writeAccess->allows($request->user()),
            'contacts' => $contacts,
            'contactOptions' => $contactOptions,
            'editingContact' => $editingContact,
            'filters' => $filters,
            'groups' => $groups,
            'jsonPayload' => $this->payload($contacts),
            'roles' => [Contact::ROLE_USER => 'User', Contact::ROLE_ADMIN => 'Administrator'],
            'sorts' => ContactDirectoryQuery::SORTS,
            'sourceJson' => $dataset->sourceJson(),
            'sourceLabel' => $dataset->sourceLabel(),
            'stats' => [
                'contacts' => Contact::query()->count(),
                'active' => Contact::query()->where('is_active', true)->count(),
                'admins' => Contact::query()->where('role', Contact::ROLE_ADMIN)->count(),
                'groups' => $groups->count(),
            ],
        ]);
    }

    public function data(Request $request, ContactDirectoryQuery $directory): JsonResponse
    {
        $filters = $directory->filters($request);
        $contacts = $directory->build($filters)->get();

        return response()->json([
            'filters' => $filters,
            'count' => $contacts->count(),
            'data' => $this->payload($contacts),
        ]);
    }

    public function store(StoreModule9aContactRequest $request): RedirectResponse
    {
        $contact = Contact::query()->create($request->validated());

        return redirect()
            ->route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()])
            ->with('status', "Created contact #{$contact->getKey()} for {$contact->fullName()}.");
    }

    public function update(UpdateModule9aContactRequest $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validated());

        return redirect()
            ->route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()])
            ->with('status', "Updated contact #{$contact->getKey()}.");
    }

    public function updateDetails(UpdateModule9aContactDetailsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $contact = Contact::query()->findOrFail((int) $validated['contact_id']);

        $contact->update([
            'phone' => $validated['details_phone'],
            'company' => $validated['details_company'],
            'contact_group_id' => $validated['details_contact_group_id'],
        ]);

        return redirect()
            ->route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()])
            ->with('status', "Updated phone, company, and group for contact #{$contact->getKey()}.");
    }

    public function destroy(Request $request, Contact $contact, Module9aWriteAccess $writeAccess): RedirectResponse
    {
        abort_unless($writeAccess->allows($request->user()), 403);

        $id = $contact->getKey();
        $contact->delete();

        return redirect()
            ->route('assignments.module9a.contacts.index')
            ->with('status', "Deleted contact #{$id}.");
    }

    public function destroyById(DeleteModule9aContactByIdRequest $request): RedirectResponse
    {
        $id = (int) $request->validated('contact_id');
        Contact::query()->findOrFail($id)->delete();

        return redirect()
            ->route('assignments.module9a.contacts.index')
            ->with('status', "Deleted contact #{$id} by ID.");
    }

    /**
     * @param  Collection<int, Contact>  $contacts
     * @return list<array{id: int, first_name: string, last_name: string, email: string, phone: string|null, company: string|null, group: string|null, role: string, is_active: bool, created_at: string|null, updated_at: string|null}>
     */
    private function payload(Collection $contacts): array
    {
        return $contacts->map(static fn (Contact $contact): array => [
            'id' => (int) $contact->getKey(),
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'company' => $contact->company,
            'group' => $contact->group?->name,
            'role' => $contact->role,
            'is_active' => $contact->is_active,
            'created_at' => $contact->created_at?->toIso8601String(),
            'updated_at' => $contact->updated_at?->toIso8601String(),
        ])->values()->all();
    }
}
