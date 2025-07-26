<?php

namespace App\Http\Controllers\Api;
use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class VolunteerRegistrationController
{


    public function register(Request $request, $courseId)
    {
        $course = TrainingCourse::where('is_announced', true)->find($courseId);
        if (!$course) {
            return response()->json(['message' => 'الدورة التدريبية غير موجودة'], 404);
        }

        // التحقق من أن المستخدم مسجل دخول وهو متطوع أو مستفيد
        $user = auth()->user();
        if (!$user || !in_array($user->role_id, [5, 6])) {
            return response()->json([
                'success' => false,
                'message' => 'يجب أن تكون متطوعاً أو مستفيداً مسجلاً'
            ], 403);
        }

        // التحقق من عدم التسجيل المسبق
        if ($course->volunteers()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مسجل بالفعل على هذه الدورة'
            ], 400);
        }

        // التحقق من السعة المتاحة
        if ($course->current_volunteers >= $course->max_volunteers) {
            return response()->json([
                'success' => false,
                'message' => 'تم اكتمال العدد المسموح للمشاركين'
            ], 400);
        }

        DB::transaction(function () use ($course, $user) {
            $course->volunteers()->attach($user->id, [  // $user->id يجب أن يكون رقميًا
                'status' => 'pending',
                'registered_at' => now(),
                'user_type' => in_array($user->role_id, [5, 6]) ? 'volunteer' : 'beneficiary'
            ]);

            $course->increment('current_volunteers');
        });



        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل في الدورة بنجاح',
            'remaining_slots' => $course->max_volunteers - $course->current_volunteers
        ]);
    }





    public function showRegistrations($courseId)
    {   $user = auth()->user();
        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return response()->json(['message' => 'هذا الإجراء مسموح فقط للادمن والبروجيكت مانجر!'], 403);
        }

        $course = TrainingCourse::with(['volunteers' => function($query) {
            $query->select('users.id', 'users.name', 'users.email', 'course_volunteer.status', 'course_volunteer.registered_at');
        }])->find($courseId);

        if (!$course) {
            return response()->json(['message' => 'الدورة التدريبية غير موجودة'], 404);
        }




        return response()->json([
            'course' => $course->only('id', 'title', 'current_volunteers', 'max_volunteers'),
            'volunteers' => $course->volunteers
        ]);
    }

}
