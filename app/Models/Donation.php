<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'amount',
        'currency',
        'donor_email',
        'stripe_payment_id'
    ];
}
