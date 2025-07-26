<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->story_content,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'status' => $this->status,
            'submitted_by' => new UserResource($this->whenLoaded('user')),
            'is_featured' => $this->is_featured,
            'submission_date' => $this->submission_date,
        ];
    }
}
