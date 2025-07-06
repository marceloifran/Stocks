<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class PurchaseOrder extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'stock_id',
        'quantity',
        'status',
        'notes',
        'order_column',
        'requested_date',
        'ordered_date',
        'received_date',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'ordered_date' => 'date',
        'received_date' => 'date',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendiente' => 'danger',
            'pedido' => 'warning',
            'comprado' => 'info',
            'recibido' => 'success',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendiente' => 'Pendiente',
            'pedido' => 'Pedido',
            'comprado' => 'Comprado',
            'recibido' => 'Recibido',
            default => $this->status,
        };
    }
}
