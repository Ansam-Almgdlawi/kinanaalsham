<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryType extends Model
{
    protected $fillable = ['name', 'description'];

    public function beneficiaries()
    {
        return $this->hasMany(BeneficiaryDetail::class);
    }

}
