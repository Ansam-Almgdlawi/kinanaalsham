<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TrainingCourse;

class CourseVoteRequest extends FormRequest
{
    public function authorize()
    {
return true;
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
