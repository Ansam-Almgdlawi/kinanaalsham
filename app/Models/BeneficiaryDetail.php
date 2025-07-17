<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryDetail extends Model
{
    protected $fillable = [
        'user_id', 'beneficiary_type_id', 'civil_status',
        'gender', 'birth_date', 'address', 'family_members_count',
        'case_details', 'registration_date', 'last_document_update'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(BeneficiaryType::class, 'beneficiary_type_id');
    }

}
