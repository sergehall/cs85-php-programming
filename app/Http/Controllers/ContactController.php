<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function index(): View
    {
        return view('contacts.index', [
            'contacts' => Contact::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('contacts.create');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        Contact::query()->create($request->validated());

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact created successfully.');
    }

    public function show(Contact $contact): RedirectResponse
    {
        return redirect()->route('contacts.edit', $contact);
    }

    public function edit(Contact $contact): View
    {
        return view('contacts.edit', ['contact' => $contact]);
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validated());

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact updated successfully.');
    }

    public function destroy(
        Request $request,
        Contact $contact,
        Module9aWriteAccess $writeAccess,
    ): RedirectResponse {
        abort_unless($writeAccess->allows($request->user()), 403);

        $contact->delete();

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact deleted successfully.');
    }
}
