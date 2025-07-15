<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVote extends Model
{
    protected $table = 'course_votes';
    public $timestamps = false; // تعطيل التوقيتات التلقائية
    protected $fillable = [
        'course_id',
        'user_id',
        'voted_at'
    ];

    protected $casts = [
        'voted_at' => 'datetime'
    ];

    // علاقة مع الدورة التدريبية
    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
