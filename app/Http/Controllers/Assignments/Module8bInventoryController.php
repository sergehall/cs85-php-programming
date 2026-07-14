<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class Module8bInventoryController extends Controller
{
    /**
     * @var array<string, array{label: string, column: string, direction: 'asc'|'desc', secondary: string}>
     */
    private const SORTS = [
        'category_name_asc' => [
            'label' => 'Category, then item A-Z',
            'column' => 'category',
            'direction' => 'asc',
            'secondary' => 'item_name',
        ],
        'item_name_asc' => [
            'label' => 'Item name A-Z',
            'column' => 'item_name',
            'direction' => 'asc',
            'secondary' => 'id',
        ],
        'quantity_desc' => [
            'label' => 'Quantity high to low',
            'column' => 'quantity',
            'direction' => 'desc',
            'secondary' => 'item_name',
        ],
        'quantity_asc' => [
            'label' => 'Quantity low to high',
            'column' => 'quantity',
            'direction' => 'asc',
            'secondary' => 'item_name',
        ],
        'purchase_date_desc' => [
            'label' => 'Newest purchase first',
            'column' => 'purchase_date',
            'direction' => 'desc',
            'secondary' => 'item_name',
        ],
        'purchase_date_asc' => [
            'label' => 'Oldest purchase first',
            'column' => 'purchase_date',
            'direction' => 'asc',
            'secondary' => 'item_name',
        ],
    ];

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $sort = self::SORTS[$filters['sort']];

        $items = Item::query()
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = '%'.$filters['search'].'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('item_name', 'like', $search)
                        ->orWhere('category', 'like', $search);
                });
            })
            ->when($filters['category'] !== '', fn (Builder $query): Builder => $query->where('category', $filters['category']))
            ->when($filters['min_quantity'] !== null, fn (Builder $query): Builder => $query->where('quantity', '>=', $filters['min_quantity']))
            ->orderBy($sort['column'], $sort['direction'])
            ->orderBy($sort['secondary'])
            ->get();

        $categories = Item::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('assignments.module8b.inventory', [
            'categories' => $categories,
            'filters' => $filters,
            'items' => $items,
            'sorts' => self::SORTS,
        ]);
    }

    /**
     * @return array{search: string, category: string, min_quantity: int|null, sort: string}
     */
    private function filters(Request $request): array
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $minimumInput = $request->query('min_quantity');
        $sort = (string) $request->query('sort', 'category_name_asc');
        $minimum = filter_var($minimumInput, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0],
        ]);

        return [
            'search' => mb_substr($search, 0, 100),
            'category' => mb_substr($category, 0, 50),
            'min_quantity' => $minimum === false ? null : $minimum,
            'sort' => isset(self::SORTS[$sort]) ? $sort : 'category_name_asc',
        ];
    }
}
