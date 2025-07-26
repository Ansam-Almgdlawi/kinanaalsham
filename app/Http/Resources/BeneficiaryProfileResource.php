<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryProfileResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'profilePictureUrl' => $this->profile_picture_url,
            'status' => $this->status,

            'details' => [
                'civilStatus' => $this->details->civil_status ?? null,
                'gender' => $this->details->gender ?? null,
                'address' => $this->details->address ?? null,
                'familyCount' => $this->details->family_members_count ?? null,
                'caseDetails' => $this->details->case_details ?? null,
            ],

            'documents' => $this->documents,

        ];
    }
}
