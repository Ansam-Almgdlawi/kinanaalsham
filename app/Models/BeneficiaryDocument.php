<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryDocument extends Model
{
    protected $fillable = [
        'beneficiary_user_id', 'document_type', 'file_path',
        'file_name', 'mime_type', 'verification_status', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
    }

}
