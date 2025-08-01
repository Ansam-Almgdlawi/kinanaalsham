<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TrainingCourse;

class AnnounceCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course_id' => [
                'required',
                'exists:training_courses,id',
//                function ($attribute, $value, $fail) {
//                    $course = TrainingCourse::find($value);
//                    if (!$course->votes()->exists()) {
//                        $fail('يجب أن تحتوي الدورة على تصويتات على الأقل');
//                    }
//                }
            ],
            'announcement_text' => 'nullable|string|max:500',
            'max_volunteers' => 'required|integer|min:1' // إضافة هذا الحقل
        ];
    }
}
