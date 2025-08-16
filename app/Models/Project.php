<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $fillable = [
        'project_number',
        'name',
        'description',
        'budget',
        'objective',
        'status',
        'start_date',
        'end_date',
        'requirements',
        'max_beneficiaries',
        'current_beneficiaries',
        'total_revenue',
        'total_expenses',
        'created_by_user_id'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
    public function beneficiaries()
    {
        return $this->hasMany(ProjectBeneficiary::class);
    }
    // في Project.php
    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'project_volunteer', 'project_id', 'user_id')
            ->withPivot('status', 'motivation');
    }



    public function ratings()
    {
        return $this->hasMany(ProjectRating::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }
    public function scopeHasAvailableSlots($query)
    {
        return $query->where(function($q) {
            $q->where('max_beneficiaries', 0)
                ->orWhereRaw('max_beneficiaries > (
              SELECT COUNT(*) FROM project_beneficiaries
              WHERE project_id = projects.id AND status = "approved"
          )');
        });
    }
}
