<?php

// app/Http/Requests/EventCreateRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // فقط الأدمن يستطيع إنشاء فعالية
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type_id' => 'nullable|exists:event_types,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location_text' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'target_audience' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'is_public' => 'required|boolean',
            'supervisor_user_id' => 'nullable|exists:users,id', // المشرف (يمكن أن يكون null)
        ];
    }
}
