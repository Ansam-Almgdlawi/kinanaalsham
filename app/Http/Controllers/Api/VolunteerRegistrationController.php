<?php

namespace App\Http\Controllers\Api;
use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class VolunteerRegistrationController
{
    public function register(Request $request, $courseId)
    {
        $course = TrainingCourse::where('is_announced', true)->findOrFail($courseId);

        // التحقق من الشروط
        if ($course->volunteers()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مسجل بالفعل على هذه الدورة'
            ], 400);
        }

        if ($course->current_volunteers >= $course->max_volunteers) {
            return response()->json([
                'success' => false,
                'message' => 'تم اكتمال العدد المسموح للمتطوعين'
            ], 400);
        }

        // التسجيل
        DB::transaction(function () use ($course) {
            $course->volunteers()->attach(auth()->id(), [
                'status' => 'approved',
                'registered_at' => now()
            ]);

            $course->increment('current_volunteers');
        });

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل في الدورة بنجاح',
            'remaining_slots' => $course->max_volunteers - $course->current_volunteers
        ]);
    }
}
