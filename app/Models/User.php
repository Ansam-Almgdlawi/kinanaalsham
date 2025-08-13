<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role_id',
        'status',
        'profile_picture_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the volunteer details associated with the user.
     */
    public function volunteerDetails()
    {
        return $this->hasOne(VolunteerDetail::class);
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user is a volunteer.
     *
     * @return bool
     */
    public function isVolunteer()
    {
        return $this->role && $this->role->name === 'Volunteer';
    }

    public function beneficiaryDetail()
    {
        return $this->hasOne(BeneficiaryDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(BeneficiaryDocument::class, 'beneficiary_user_id');
    }

    public function trainingCourses()
    {
        return $this->belongsToMany(TrainingCourse::class, 'course_volunteer', 'user_id', 'course_id');
    }

    public function courseVotes()
    {
        return $this->hasMany(CourseVote::class);
    }

    public function registeredCourses()
    {
        return $this->belongsToMany(TrainingCourse::class, 'course_volunteer')
            ->withPivot(['status', 'registered_at'])
            ->withTimestamps();
    }
    public function details()
    {
        return $this->hasOne(BeneficiaryDetail::class, 'user_id');
    }
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_volunteer')
            ->withPivot('status', 'user_type');
    }
    public function volunteerProjects()
    {
        return $this->belongsToMany(Project::class, 'project_volunteer')
            ->withPivot('status', 'registered_at');
    }

}
