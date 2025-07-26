<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقة مع الفعاليات (إذا كنت تحتاجها)
    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }
}
