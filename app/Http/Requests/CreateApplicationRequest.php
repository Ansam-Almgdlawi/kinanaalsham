<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'opportunity_id' => ['required', 'exists:opportunities,id'], // يجب أن تكون الفرصة موجودة
            'cover_letter' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'opportunity_id.required' => 'معرف الفرصة مطلوب.',
            'opportunity_id.exists' => 'الفرصة المحددة غير موجودة.',
        ];
    }
}
