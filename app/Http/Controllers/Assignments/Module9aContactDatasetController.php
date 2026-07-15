<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use App\Services\Modules\Module9A\ContactDatasetImporter;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class Module9aContactDatasetController extends Controller
{
    public function store(
        Request $request,
        ContactDatasetImporter $dataset,
        Module9aWriteAccess $writeAccess,
    ): RedirectResponse {
        abort_unless($writeAccess->allows($request->user()), 403);

        $result = $dataset->import();

        return redirect()
            ->route('assignments.module9a.contacts.index')
            ->with(
                'status',
                "Imported {$result['contacts']} contacts and {$result['groups']} groups from JSON ({$result['created']} created, {$result['updated']} updated).",
            );
    }

    public function destroy(
        Request $request,
        ContactDatasetImporter $dataset,
        Module9aWriteAccess $writeAccess,
    ): RedirectResponse {
        abort_unless($writeAccess->allows($request->user()), 403);

        $result = $dataset->clear();

        return redirect()
            ->route('assignments.module9a.contacts.index')
            ->with('status', "Cleared {$result['contacts']} contacts and {$result['groups']} groups from the Module 9 tables.");
    }
}
