<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBeneficiary extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'benefit_details'
    ];

    // العلاقة مع المشروع
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
