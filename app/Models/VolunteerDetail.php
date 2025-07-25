<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerDetail extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'skills',
        'interests',
        'availability_schedule',
        'emergency_contact_name',
        'emergency_contact_phone',
        'date_joined_from_form',
        'total_points',
        'total_hours_volunteered',
        'volunteering_type_preference',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_joined_from_form' => 'datetime',
        'total_hours_volunteered' => 'decimal:2',
        'total_points' => 'integer',
        'availability_schedule' => 'array',
    ];

    /**
     * Get the user that owns the volunteer details.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
