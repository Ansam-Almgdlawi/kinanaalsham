<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image',

            'beneficiary_type_id' => 'required|exists:beneficiary_types,id',
            'civil_status' => 'nullable|in:single,married,divorced,widowed,unknown',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'family_members_count' => 'nullable|integer|min:1',
            'case_details' => 'nullable|string',
            'registration_date' => 'required|date',

            'documents' => 'nullable|array',
            'documents.*.document_type' => 'required|string',
            'documents.*.file' => 'required|file',
            'documents.*.notes' => 'nullable|string',
        ];
    }

}
