<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOpportunityStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role_id === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:open,closed,filled'],
        ];
    }
    public function messages(): array
    {
        return [
            'status.required' => 'حالة الفرصة مطلوبة.',
            'status.in' => 'حالة الفرصة يجب أن تكون إما "open" أو "closed" أو "filled".',
        ];
    }
}
