<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'opportunity_id' => $this->opportunity_id,
            'opportunity_title' => $this->whenLoaded('opportunity', fn() => $this->opportunity->title), // عرض عنوان الفرصة
            'applicant_user_id' => $this->applicant_user_id,
            'applicant_name' => $this->whenLoaded('applicant', fn() => $this->applicant->name), // عرض اسم المتقدم
            'applicant_email' => $this->whenLoaded('applicant', fn() => $this->applicant->email), // عرض ايميل المتقدم
            'application_date' => $this->application_date->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'cover_letter' => $this->cover_letter,
            'reviewed_by_user_id' => $this->reviewed_by_user_id,
            'reviewer_name' => $this->whenLoaded('reviewer', fn() => $this->reviewer->name),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
