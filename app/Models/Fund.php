<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = ['name', 'balance'];
    protected $casts = [
        'balance' => 'decimal:2',
    ];
}
