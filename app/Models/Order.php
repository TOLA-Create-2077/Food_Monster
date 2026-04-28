<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'branch_id',
        'customer_id',
        'created_by',
        'updated_by',
        'order_no',
        'order_date',
        'delivery_date',
        'delivery_time',
        'chef_group',
        'order_source',
        'customer_category',
        'delivery_fee',
        'subtotal',
        'discount',
        'grand_total',
        'payment_status',
        'order_status',
        'name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'extra_info',
        'remark',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }
}