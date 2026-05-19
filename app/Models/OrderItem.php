<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_variate_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percent',
        'sub_total',
        'grand_total',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'discount_percent' => 'float',
        'sub_total' => 'float',
        'grand_total' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getItemNameAttribute(): string
    {
        return (string) ($this->description ?? 'Order Item');
    }

    public function getQtyAttribute(): int
    {
        return (int) ($this->quantity ?? 0);
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->grand_total ?? 0);
    }

    public function getDiscountAttribute(): float
    {
        return (float) ($this->discount_percent ?? 0);
    }
}