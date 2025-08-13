<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
//    public function items()
//    {
//        return $this->hasMany(InventoryItem::class);
//    }
}
