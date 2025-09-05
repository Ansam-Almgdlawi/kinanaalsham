<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVolunteer extends Model
{
    protected $table = 'course_volunteer'; // اسم الجدول الصحيح

    protected $fillable = [
        'course_id',
        'user_id',
        'user_type',  // volunteer أو beneficiary
        'status',
        'registered_at',
    ];

    // علاقة بالمستخدم
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // علاقة بالكورس
    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }
}
