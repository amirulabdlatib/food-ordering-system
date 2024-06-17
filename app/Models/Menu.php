<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }
}