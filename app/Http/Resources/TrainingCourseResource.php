<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingCourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'trainer' => $this->trainer_name,
            'schedule' => [
                'start_date' => $this->start_date?->format('Y-m-d'),
                'end_date' => $this->end_date?->format('Y-m-d'),
                'duration_hours' => $this->duration_hours,
            ],
            'location' => $this->location,
            'target_audience' => $this->target_audience_description,
            'admin' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
