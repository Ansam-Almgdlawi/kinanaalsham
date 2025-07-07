<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:16|max:100',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:volunteer_applications,email',
            'skills' => 'required|string',
            'interests' => 'required|in:Educational,Medicine,Organizational,Media,technical',
            'available_times' => 'required|array',
            'available_times.*' => 'string|max:100',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'notes' => 'string',
        ];
    }
}
