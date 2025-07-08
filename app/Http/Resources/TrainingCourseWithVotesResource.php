<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingCourseWithVotesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total_votes' => $this->votes_count,
            'votes' => $this->votes,
            'remaining_hours' => $this->resource['remaining_hours'] ?? null,
        ];
    }
}
