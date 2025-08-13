<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPost extends Model
{
    protected $fillable = [
        'project_id',
        'admin_id',
        'content',
        'media'
    ];

    protected $casts = [
        'media' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
