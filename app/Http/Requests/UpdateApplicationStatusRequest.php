<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateApplicationStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role_id === 1;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending_review,accepted,rejected,withdrawn'],
            'review_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'حالة الطلب مطلوبة.',
            'status.in' => 'حالة الطلب يجب أن تكون إحدى القيم التالية: pending_review, accepted, rejected, withdrawn.',
        ];
    }
}
