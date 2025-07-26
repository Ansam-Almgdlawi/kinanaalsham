<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $fillable = [
        'beneficiary_user_id',
        'request_details',
        'address',
        'required_specialization',
        'status',
        'assigned_volunteer_user_id',
        'resolution_details',
    ];
    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
    }
    public function assignedVolunteer() {
        return $this->belongsTo(User::class, 'assigned_volunteer_user_id');
    }
}
