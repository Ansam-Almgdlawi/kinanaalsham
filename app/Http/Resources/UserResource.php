<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'profile_picture_url' => $this->profile_picture_url
                ? asset('storage/' . $this->profile_picture_url)
                : null,

            'email_verified_at' => $this->email_verified_at,
            'role' => $this->role ? [
                'id' => $this->role->id,
                'name' => $this->role->name,
            ] : null,
            'volunteer_details' => $this->whenLoaded('volunteerDetails', function () {
                return [
                    'skills' => $this->volunteerDetails->skills,
                    'interests' => $this->volunteerDetails->interests,
                    'availability_schedule' => $this->volunteerDetails->availability_schedule,
                    'total_points' => $this->volunteerDetails->total_points,
                    'total_hours_volunteered' => $this->volunteerDetails->total_hours_volunteered,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
