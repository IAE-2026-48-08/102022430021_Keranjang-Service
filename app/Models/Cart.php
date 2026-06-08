<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_name',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}