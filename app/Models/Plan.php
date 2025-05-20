<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 
        'type',
        'price',
        'description',
        'options',
    ];

    public function carts()
    {
        return $this->morphMany(Cart::class, 'itemable', 'item_type', 'item_id');
    }
}