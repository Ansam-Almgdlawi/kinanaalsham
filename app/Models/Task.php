<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    use HasFactory;

    protected $fillable = ['roadmap_id', 'description', 'duration_in_days', 'required_volunteers'];

    public function roadmap()
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'task_volunteer', 'task_id', 'volunteer_id');
    }
}
