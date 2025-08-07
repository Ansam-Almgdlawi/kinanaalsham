<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionRecord extends Model
{

    protected $fillable = ['distribution_id', 'user_id', 'has_received'];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
