<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'selling_price',
        'tax_rate',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock &&
            $this->stock->quantity <= $this->low_stock_threshold;
    }
}