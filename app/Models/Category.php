<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";

    protected $fillable = [
        'branch_id',
        'code',
        'title_en',
        'title_km',
        'image',
        'status'
    ];
}