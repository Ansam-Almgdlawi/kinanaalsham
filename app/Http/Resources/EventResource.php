<?php

// app/Http/Resources/EventResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'event_type' => $this->eventType->name,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'location' => $this->location_text,
            'status' => $this->status,
            'supervisor' => $this->supervisor ? $this->supervisor->name : null,
            'created_at' => $this->created_at,
        ];
    }

    private function getMediaUrls()
    {
        return $this->media ? array_map(function($path) {
            return asset('storage/'.$path);
        }, json_decode($this->media, true)) : [];
    }
}
