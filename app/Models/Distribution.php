<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{

    protected $fillable = [
        'title',
        'beneficiary_type_id',
        'inventory_item_id',
        'quantity_per_user'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function records()
    {
        return $this->hasMany(DistributionRecord::class);
    }

    public function beneficiaryType()
    {
        return $this->belongsTo(BeneficiaryType::class);
    }

}
