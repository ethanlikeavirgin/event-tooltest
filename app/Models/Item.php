<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'name', 
        'price',
        'description',
        'date',
        'max',
        'file_path',
        'user_id',
    ];
    public function carts()
    {
        return $this->morphMany(Cart::class, 'itemable', 'item_type', 'item_id');
    }
}
