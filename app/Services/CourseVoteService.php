<?php

namespace App\Services;

use App\Http\Controllers\Api\TrainingCourseController;
use App\Models\TrainingCourse;
use App\Repositories\CourseVoteRepository;
use App\Http\Resources\TrainingCourseWithVotesResource;
use Illuminate\Support\Facades\Auth;

class CourseVoteService
{
    protected $voteRepository;

    public function __construct(CourseVoteRepository $voteRepository)
    {
        $this->voteRepository = $voteRepository;
    }

    public function vote($courseId)
    {
        // 1. البحث عن الدورة مع التحقق من وجودها
        $course = TrainingCourse::find($courseId);


        if (!$course) {
            return [
                'success' => false,
                'message' => 'الدورة التدريبية غير موجودة'
            ];
        }

        // 2. التحقق من وجود تاريخ الإنشاء
        if (!$course->created_at) {
            return [
                'success' => false,
                'message' => 'تاريخ إنشاء الدورة غير محدد'
            ];
        }

        // 3. تحويل التاريخ إلى تنسيق نظامي (Y-m-d H:i:s)
        $createdAt = $course->created_at->format('Y-m-d H:i:s');
        $currentTime = now()->format('Y-m-d H:i:s');

        // 4. حساب الفرق بالساعات
        $hoursPassed = $course->created_at->diffInHours(now());

        // 5. التحقق من انتهاء فترة التصويت (48 ساعة)
        if ($hoursPassed > 48) {
            return [
                'success' => false,
                'message' => 'انتهت فترة التصويت',
                'details' => [
                    'تاريخ_الإنشاء' => $createdAt,
                    'الوقت_الحالي' => $currentTime,
                    'الساعات_المنقضية' => $hoursPassed
                ]
            ];
        }


        // 6. إنشاء التصويت
        $vote = $this->voteRepository->createVote($course, auth()->user());

        // 7. إرجاع النتيجة
        return [
            'success' => true,
            'total_votes' => $course->votes()->count(),
            'remaining_hours' => 48 - $hoursPassed,
            'time_details' => [
                'تاريخ_الإنشاء' => $createdAt,
                'الوقت_الحالي' => $currentTime,
                'الساعات_المنقضية' => $hoursPassed,
                'الساعات_المتبقية' => 48 - $hoursPassed
            ]
        ];
    }

    public function getAllCoursesWithVotes()
    {
        return TrainingCourse::with('votes')->get();
    }
}
