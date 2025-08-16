<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_type_id',
        'start_datetime',
        'end_datetime',
        'location_text',
        'latitude',
        'longitude',
        'status',
        'organizer_user_id',
        'supervisor_user_id',
        'target_audience',
        'max_participants',
        'is_public',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_public' => 'boolean',
    ];

    // العلاقة مع جدول أنواع الفعاليات (event_types)
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    // العلاقة مع المنظم (الأدمن)
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }

    // العلاقة مع المشرف (يمكن أن تكون null)
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'event_volunteer')
            ->wherePivot('user_type', 'volunteer');
    }

    public function ratings()
    {
        return $this->hasMany(EventRating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->average('rating');
    }
}
