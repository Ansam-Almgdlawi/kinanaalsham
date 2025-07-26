<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiaryProfileRequest extends FormRequest
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
            'civil_status' => 'nullable|in:single,married,divorced,widowed,unknown',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:255',
            'family_members_count' => 'nullable|integer|min:0',
            'case_details' => 'nullable|string',

            'documents.*.name' => 'required_with:documents|string|max:255',
            'documents.*.file' => 'required_with:documents|file|mimes:pdf,jpg,png|max:2048',
            'documents.*.type' => 'required_with:documents|string|max:100',
        ];
    }

}
