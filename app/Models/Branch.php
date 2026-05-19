<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'shop_id',
        'title',
        'location',
        'embed_map',
        'opening_hour',
        'close_hour',
        'phone',
        'email',
        'telegram',
        'tax_id',
        'registration_number',
        'description',
        'status',
        'image',
        'order_alert_chat_id',
        'stock_alert_chat_id',
        'cover',
        'lat_long',
        'user_id',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'branch_id');
    }

    public function getNameAttribute(): string
    {
        if (!is_string($this->title) || trim($this->title) === '') {
            return 'Branch #' . $this->id;
        }

        $decoded = json_decode($this->title, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach (['en', 'km', 'title', 'name'] as $key) {
                if (!empty($decoded[$key]) && is_string($decoded[$key])) {
                    return trim($decoded[$key]);
                }
            }

            foreach ($decoded as $item) {
                if (is_string($item) && trim($item) !== '') {
                    return trim($item);
                }
            }
        }

        return trim($this->title);
    }
}