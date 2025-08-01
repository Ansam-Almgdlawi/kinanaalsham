<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventVolunteerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'volunteer_user_id' => $this->volunteer_user_id,
            'status' => $this->registration_status,
            'hours' => $this->hours_volunteered,
            'feedback' => [
                'rating' => $this->feedback_rating,
                'comment' => $this->feedback_comment,
            ],
        ];
    }
}
