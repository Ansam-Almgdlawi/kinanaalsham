<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TrainingCourse;

class CourseVoteRequest extends FormRequest
{
    public function authorize()
    {
        $course = TrainingCourse::find($this->courseId);
        return $course && !$course->votes()->where('user_id', auth()->id())->exists();
    }

    public function rules()
    {
        return [
            //'courseId' => 'required|exists:training_courses,id'
        ];
    }

    public function messages()
    {
        return [
            'courseId.exists' => 'الدورة التدريبية غير موجودة',
        ];
    }
}
