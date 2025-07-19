<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $fillable = [
        'submitted_by_user_id',
        'title',
        'story_content',
        'submission_date',
        'status',
        'is_featured',
        'image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }
}
