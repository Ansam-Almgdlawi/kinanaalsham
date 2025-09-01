<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    use HasFactory;

    /**
     * الأعمدة التي يمكن تعيينها بشكل جماعي (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'location_text',
        'start_date',
        'end_date',
        'requirements',
        'is_remote',
        'skills',
        'category',
    ];

    /**
     * تحويل أنواع الأعمدة تلقائيًا.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date', // لتحويل التاريخ إلى كائن Carbon
        'end_date' => 'date',   // لتحويل التاريخ إلى كائن Carbon
        'is_remote' => 'boolean', // لتحويل 0/1 إلى true/false

    ];



    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeVolunteering($query)
    {
        return $query->where('type', 'volunteering');
    }
}
