<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class VolunteerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'age', 'gender', 'phone_number',
        'email', 'skills', 'interests', 'available_times',
        'status', 'notes', 'cv_path'
    ];

    protected $casts = [
        'available_times' => 'array',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['formatted_date'];

    public function getInterestsTextAttribute()
    {
        return __($this->interests);
    }

    // Scope for filtering by status
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getAvailableTimesTextAttribute()
    {
        return collect($this->available_times)
            ->map(fn($hours, $day) => ucfirst($day) . ': ' . $hours)
            ->implode(', ');
    }
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('d-m-Y');
    }
}
