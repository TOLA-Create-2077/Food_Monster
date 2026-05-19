<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'branch_id',
        'order_source_id',
        'code',
        'customer_id',
        'name',
        'phone',
        'address',
        'location',
        'delivery_id',
        'delivery_fee',
        'discount_percent',
        'sub_total',
        'grand_total',
        'receive_amount',
        'scheduled_date',
        'invoice_date',
        'order_date',
        'status',
        'remark',
        'extra_info',
        'proof_image',
        'fcm_token',
        'order_resource_from',
        'customer_type',
        'customer_preference',
        'membership_type',
        'membership_number',
        'benefits',
        'payment_type',
        'is_locked',
        'order_from',
        'customer_category',
        'user_id',
    ];

    protected $casts = [
        'delivery_fee' => 'float',
        'discount_percent' => 'float',
        'sub_total' => 'float',
        'grand_total' => 'float',
        'receive_amount' => 'float',
        'scheduled_date' => 'datetime',
        'invoice_date' => 'datetime',
        'order_date' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function getOrderNoAttribute(): string
    {
        return (string) ($this->code ?? ('ORDER-' . $this->id));
    }

    public function getOrderStatusAttribute(): string
    {
        return (string) ($this->status ?? 'PENDING');
    }

    public function getPaymentStatusAttribute(): string
    {
        $grandTotal = (float) ($this->grand_total ?? 0);
        $receiveAmount = (float) ($this->receive_amount ?? 0);

        if ($receiveAmount <= 0) {
            return 'pending';
        }

        if ($grandTotal > 0 && $receiveAmount >= $grandTotal) {
            return 'paid';
        }

        return 'partial';
    }

    public function getDiscountAttribute(): float
    {
        $subTotal = (float) ($this->sub_total ?? 0);
        $discountPercent = (float) ($this->discount_percent ?? 0);

        return round($subTotal * ($discountPercent / 100), 2);
    }

    public function getQtyAttribute(): int
    {
        return (int) $this->items->sum('qty');
    }

    public function getLatitudeAttribute(): ?string
    {
        if (empty($this->location) || !str_contains((string) $this->location, ',')) {
            return null;
        }

        return trim(explode(',', (string) $this->location)[0]);
    }

    public function getLongitudeAttribute(): ?string
    {
        if (empty($this->location) || !str_contains((string) $this->location, ',')) {
            return null;
        }

        return trim(explode(',', (string) $this->location)[1] ?? '');
    }
}