<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $purchase_date
 * @property int $quantity
 */
class Item extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'item_name',
        'category',
        'quantity',
        'purchase_date',
    ];

    protected $connection = 'module8b';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'quantity' => 'integer',
        ];
    }
}
