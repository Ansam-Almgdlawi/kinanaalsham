<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPost extends Model
{

    protected $fillable = ['event_id', 'admin_id', 'content', 'media'];

    protected $casts = [
        'media' => 'array'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
