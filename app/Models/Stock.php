<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'low_stock_threshold',
        'last_restocked_at',
    ];

    protected $casts = [
        'last_restocked_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return $this->product !== null
            && $this->quantity <= $this->product->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }
}
