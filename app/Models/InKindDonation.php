<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InKindDonation extends Model
{

    use HasFactory;

    protected $fillable = [
        'phone_number',
        'category',
        'item_name',
        'quantity',
        'unit',
        'donated_at',
        'status',
    ];

    protected $casts = [
        'donated_at' => 'datetime',
    ];
}
