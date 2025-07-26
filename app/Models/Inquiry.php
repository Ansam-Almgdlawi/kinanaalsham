<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sender_user_id',
        'subject',
        'message',
        'admin_reply',
        'replied_at',
    ];

    protected $hidden = [
        'created_at','updated_at',
        'sender_name',
        'sender_email',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->replied_at)->format('d-m-Y');
    }

}
