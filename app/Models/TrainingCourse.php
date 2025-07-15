<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'trainer_name',
        'start_date',
        'end_date',
        'duration_hours',

        'location'



    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];


//    public function admin()
//    {
//        return $this->belongsTo(User::class, 'created_by_user_id');
//    }


    public function votes()
    {
        return $this->hasMany(CourseVote::class, 'course_id');
    }




}
