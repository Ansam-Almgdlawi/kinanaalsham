<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmap extends Model
{

    use HasFactory;

    protected $fillable = ['event_id', 'supervisor_id', 'title'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
