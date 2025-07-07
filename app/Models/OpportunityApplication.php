<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OpportunityApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'applicant_user_id',
        'application_date',
        'status',
        'cover_letter',
        'reviewed_by_user_id',
        'review_notes',
    ];

    protected $casts = [
        'application_date' => 'datetime',
    ];

    /**
     * الطلب ينتمي إلى فرصة معينة.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * الطلب ينتمي إلى المستخدم المتقدم.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_user_id');
    }

    /**
     * الطلب تم مراجعته بواسطة مستخدم (أدمن).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

}
