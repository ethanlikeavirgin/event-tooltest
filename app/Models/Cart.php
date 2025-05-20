<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $fillable = [
        'name',
        'item_id',
        'item_type',
        'counter',
        'user_id',
        'guest_token',
        'total',
        'type',
    ];

    public function itemable()
    {
        return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
