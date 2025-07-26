<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistanceRequest extends Model
{

    protected $fillable = [
        'beneficiary_user_id',
        'assistance_type',
        'description',
        'status',
        'admin_notes',
    ];


    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
    }

}
