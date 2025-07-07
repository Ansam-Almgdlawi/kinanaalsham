<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CreateOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role_id === 1; // افترض أن role_id=1 هو للأدمن

    }

    /**
     * الحصول على قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:job,volunteering'], // يجب أن يكون إما 'job' أو 'volunteering'
            'status' => ['sometimes', 'in:open,closed,filled'], // اختياري، والقيمة الافتراضية 'open' في DB
            'location_text' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'requirements' => ['nullable', 'string'],
            'is_remote' => ['sometimes', 'boolean'], // اختياري، والقيمة الافتراضية FALSE في DB
        ];
    }

    /**
     * تخصيص رسائل الخطأ للتحقق.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الفرصة مطلوب.',
            'description.required' => 'وصف الفرصة مطلوب.',
            'type.required' => 'نوع الفرصة مطلوب (عمل أو تطوع).',
            'type.in' => 'نوع الفرصة يجب أن يكون إما "job" أو "volunteering".',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو بعده.',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء أو مساوياً له.',
        ];
    }
}
