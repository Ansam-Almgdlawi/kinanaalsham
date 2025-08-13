<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'certificate_issued_at' => $this->issued_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'event' => [
                'id' => $this->event->id,
                'name' => $this->event->name,
                'starts_at' => $this->event->start_datetime,
                'ends_at' => $this->event->end_datetime,
            ],
        ];
    }
}
