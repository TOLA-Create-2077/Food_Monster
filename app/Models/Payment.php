<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'order_payments';

    protected $fillable = [
        'order_id',
        'payment_type_id',
        'payment_method_id',
        'amount',
        'status',
        'image',
        'remark',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getPaymentMethodAttribute(): string
    {
        return (string) ($this->remark ?: 'Payment');
    }
}