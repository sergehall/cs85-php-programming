<?php

declare(strict_types=1);

namespace App\Services\Modules\Module9A;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class ContactDirectoryQuery
{
    /**
     * @var array<string, array{label: string, column: string, direction: 'asc'|'desc', secondary: string}>
     */
    public const SORTS = [
        'name_asc' => [
            'label' => 'Last name A-Z',
            'column' => 'last_name',
            'direction' => 'asc',
            'secondary' => 'first_name',
        ],
        'name_desc' => [
            'label' => 'Last name Z-A',
            'column' => 'last_name',
            'direction' => 'desc',
            'secondary' => 'first_name',
        ],
        'email_asc' => [
            'label' => 'Email A-Z',
            'column' => 'email',
            'direction' => 'asc',
            'secondary' => 'id',
        ],
        'newest' => [
            'label' => 'Newest records first',
            'column' => 'id',
            'direction' => 'desc',
            'secondary' => 'last_name',
        ],
    ];

    /**
     * @return array{search: string, first_name: string, last_name: string, email: string, phone: string, role: string, group_id: int|null, status: string, sort: string}
     */
    public function filters(Request $request): array
    {
        $role = (string) $request->query('role', '');
        $status = (string) $request->query('status', '');
        $sort = (string) $request->query('sort', 'name_asc');
        $groupId = filter_var($request->query('group_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        return [
            'search' => $this->text($request, 'search', 120),
            'first_name' => $this->text($request, 'first_name', 100),
            'last_name' => $this->text($request, 'last_name', 100),
            'email' => $this->text($request, 'email', 255),
            'phone' => $this->text($request, 'phone', 32),
            'role' => in_array($role, [Contact::ROLE_USER, Contact::ROLE_ADMIN], true) ? $role : '',
            'group_id' => $groupId === false ? null : $groupId,
            'status' => in_array($status, ['active', 'inactive'], true) ? $status : '',
            'sort' => isset(self::SORTS[$sort]) ? $sort : 'name_asc',
        ];
    }

    /**
     * @param  array{search: string, first_name: string, last_name: string, email: string, phone: string, role: string, group_id: int|null, status: string, sort: string}  $filters
     * @return Builder<Contact>
     */
    public function build(array $filters): Builder
    {
        $sort = self::SORTS[$filters['sort']];

        return Contact::query()
            ->with('group')
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = '%'.$filters['search'].'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('company', 'like', $search);
                });
            })
            ->when($filters['first_name'] !== '', fn (Builder $query): Builder => $query->where('first_name', 'like', '%'.$filters['first_name'].'%'))
            ->when($filters['last_name'] !== '', fn (Builder $query): Builder => $query->where('last_name', 'like', '%'.$filters['last_name'].'%'))
            ->when($filters['email'] !== '', fn (Builder $query): Builder => $query->where('email', 'like', '%'.$filters['email'].'%'))
            ->when($filters['phone'] !== '', fn (Builder $query): Builder => $query->where('phone', 'like', '%'.$filters['phone'].'%'))
            ->when($filters['role'] !== '', fn (Builder $query): Builder => $query->where('role', $filters['role']))
            ->when($filters['group_id'] !== null, fn (Builder $query): Builder => $query->where('contact_group_id', $filters['group_id']))
            ->when($filters['status'] === 'active', fn (Builder $query): Builder => $query->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn (Builder $query): Builder => $query->where('is_active', false))
            ->orderBy($sort['column'], $sort['direction'])
            ->orderBy($sort['secondary']);
    }

    private function text(Request $request, string $key, int $limit): string
    {
        return mb_substr(trim((string) $request->query($key, '')), 0, $limit);
    }
}
