<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRating extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'rating',
        'comment',
        'rated_at'
    ];

    protected $casts = [
        'rated_at' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
