<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{

    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'category',
        'quantity_on_hand',
        'unit',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}
